<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Services\MembershipOAuthService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected MembershipOAuthService $oauthService;

    public function __construct(MembershipOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * Create a room reservation and synchronize loyalty points with Membership API.
     */
    public function reserve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'items' => 'nullable|array',
            'items.*.room_id' => 'required_with:items|exists:rooms,id',
            'items.*.check_in' => 'required_with:items|date',
            'items.*.check_out' => 'required_with:items|date|after:items.*.check_in',
            'items.*.guests' => 'nullable|integer|min:1',
            'items.*.nights' => 'nullable|integer|min:1',
            'items.*.quantity' => 'nullable|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'guests' => 'nullable|integer|min:1',
            'guest_name' => 'required|string|max:150',
            'guest_email' => 'required|email|max:150',
            'guest_phone' => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:1000',
            'is_member' => 'boolean',
            'member_id' => 'nullable|string',
            'member_tier' => 'nullable|string',
        ]);

        $property = Property::with('membershipProperty')->findOrFail($validated['property_id']);

        // Normalize raw items array from request or fallback to legacy single room parameters
        $rawItems = [];
        if (!empty($validated['items']) && is_array($validated['items'])) {
            $rawItems = $validated['items'];
        } elseif (!empty($validated['room_id'])) {
            $rawItems = [
                [
                    'room_id' => $validated['room_id'],
                    'check_in' => $validated['check_in'] ?? now()->format('Y-m-d'),
                    'check_out' => $validated['check_out'] ?? now()->addDays(2)->format('Y-m-d'),
                    'guests' => $validated['guests'] ?? 2,
                    'quantity' => 1,
                ],
            ];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No reservation room items provided.',
            ], 422);
        }

        $isMember = !empty($validated['is_member']) && !empty($validated['member_id']);
        $memberTier = $validated['member_tier'] ?? null;

        // Process each item and compute pricing breakdown
        $totalBase = 0;
        $totalDiscount = 0;
        $totalBeforeTaxService = 0;
        $processedItems = [];
        $transactionDetails = [];

        // Generate reservation reference code
        $reservationCode = 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        foreach ($rawItems as $idx => $rawItem) {
            $room = Room::where('property_id', $property->id)->findOrFail($rawItem['room_id']);

            $checkIn = Carbon::parse($rawItem['check_in']);
            $checkOut = Carbon::parse($rawItem['check_out']);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $quantity = max(1, (int) ($rawItem['quantity'] ?? 1));
            $guests = max(1, (int) ($rawItem['guests'] ?? 2));

            $itemBaseTotal = (float) $room->base_price * $nights * $quantity;

            // Apply loyalty tier discount if applicable
            $itemDiscount = 0;
            if ($isMember && $room->is_member_rate_applicable) {
                $discountData = $room->getDiscountedPrice($memberTier);
                if ($discountData['has_discount']) {
                    $itemDiscount = (float) $discountData['discount_amount'] * $nights * $quantity;
                }
            }

            $itemSubtotal = max(0, $itemBaseTotal - $itemDiscount);

            $totalBase += $itemBaseTotal;
            $totalDiscount += $itemDiscount;
            $totalBeforeTaxService += $itemSubtotal;

            $itemDetail = [
                'room_id' => $room->id,
                'room_code' => $room->code,
                'room_name' => $room->name,
                'room_image' => $room->image_url,
                'check_in' => $checkIn->format('Y-m-d'),
                'check_out' => $checkOut->format('Y-m-d'),
                'nights' => $nights,
                'guests' => $guests,
                'quantity' => $quantity,
                'base_price' => (float) $room->base_price,
                'base_total' => $itemBaseTotal,
                'discount_amount' => $itemDiscount,
                'subtotal' => $itemSubtotal,
            ];
            $processedItems[] = $itemDetail;

            $seq = $idx + 1;
            $transactionDetails[] = [
                'code' => "{$room->code}-{$reservationCode}-{$seq}",
                'name' => "{$room->name} ({$nights} Nights" . ($quantity > 1 ? " x{$quantity}" : '') . ")",
                'qty' => $nights * $quantity,
                'subtotal' => $itemSubtotal,
                'product_id' => $room->code,
            ];
        }

        // Tax 11% & Service Charge 10% on aggregated subtotal
        $taxRate = 11;
        $serviceRate = 10;
        $taxValue = round(($totalBeforeTaxService * $taxRate) / 100);
        $serviceValue = round(($totalBeforeTaxService * $serviceRate) / 100);
        $afterTaxService = $totalBeforeTaxService + $taxValue + $serviceValue;

        // Split guest name
        $nameParts = explode(' ', trim($validated['guest_name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $firstName;

        // Transaction response from membership API
        $pointsEarned = 0;
        $trxResponse = null;

        // Push combined transaction to Membership Platform if booking is made by an active member
        if ($isMember && $property->has_membership && $property->membershipProperty) {
            try {
                $phone = $validated['guest_phone'] ?? '+6281234567890';
                if (!str_starts_with($phone, '+')) {
                    $phone = '+' . preg_replace('/[^0-9]/', '', $phone);
                }

                $transactionPayload = [
                    'code' => $reservationCode,
                    'member_id' => $validated['member_id'],
                    'is_pending' => 0,
                    'before_tax_service' => $totalBeforeTaxService,
                    'after_tax_service' => $afterTaxService,
                    'price_type' => 'taxservice',
                    'tax_charge' => $taxRate,
                    'service_charge' => $serviceRate,
                    'tax_value' => $taxValue,
                    'service_value' => $serviceValue,
                    'detail' => $transactionDetails,
                    'guest' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $validated['guest_email'],
                        'country' => 101, // Default Indonesia
                        'phone_number' => $phone,
                    ],
                ];

                $membershipResult = $this->oauthService->pushTransaction($property, $transactionPayload);
                $trxResponse = $membershipResult;

                // Extract points earned from membership response
                if (isset($membershipResult['data']['transaction']['point'])) {
                    $pointsEarned = (int) $membershipResult['data']['transaction']['point'];
                }
            } catch (Exception $e) {
                Log::error('MEMBERSHIP_PUSH_TRANSACTION_FAILED', [
                    'reservation_code' => $reservationCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Primary room for parent relational references
        $primaryItem = $processedItems[0];
        $totalGuests = array_sum(array_column($processedItems, 'guests'));

        // Save local reservation record
        $booking = Booking::create([
            'reservation_code' => $reservationCode,
            'property_id' => $property->id,
            'room_id' => $primaryItem['room_id'],
            'booking_items' => $processedItems,
            'check_in' => $primaryItem['check_in'],
            'check_out' => $primaryItem['check_out'],
            'nights' => $primaryItem['nights'],
            'guests' => $totalGuests,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'] ?? null,
            'special_requests' => $validated['special_requests'] ?? null,
            'base_total' => $totalBase,
            'discount_amount' => $totalDiscount,
            'tax_amount' => $taxValue,
            'service_amount' => $serviceValue,
            'total_amount' => $afterTaxService,
            'is_member' => $isMember,
            'member_id' => $validated['member_id'] ?? null,
            'member_tier' => $memberTier,
            'points_earned' => $pointsEarned,
            'status' => 'CONFIRMED',
            'transaction_response' => $trxResponse,
        ]);

        $summaryRoomName = count($processedItems) > 1
            ? "{$primaryItem['room_name']} + " . (count($processedItems) - 1) . ' additional room' . (count($processedItems) > 2 ? 's' : '')
            : $primaryItem['room_name'];

        return response()->json([
            'success' => true,
            'message' => 'Reservation confirmed successfully!',
            'data' => [
                'booking_id' => $booking->id,
                'reservation_code' => $booking->reservation_code,
                'property_name' => $property->name,
                'room_name' => $summaryRoomName,
                'items' => $processedItems,
                'check_in' => $booking->check_in->format('Y-m-d'),
                'check_out' => $booking->check_out->format('Y-m-d'),
                'nights' => $booking->nights,
                'guests' => $booking->guests,
                'guest_name' => $booking->guest_name,
                'guest_email' => $booking->guest_email,
                'special_requests' => $booking->special_requests,
                'pricing' => [
                    'base_rate' => $totalBase,
                    'member_discount' => $totalDiscount,
                    'tax' => $taxValue,
                    'service' => $serviceValue,
                    'grand_total' => $afterTaxService,
                ],
                'membership' => [
                    'is_member' => $isMember,
                    'tier' => $memberTier,
                    'points_earned' => $pointsEarned,
                    'push_success' => !empty($trxResponse),
                ],
            ],
        ], 201);
    }

    /**
     * Get reservation history.
     */
    public function history(Request $request): JsonResponse
    {
        $bookings = Booking::with(['property', 'room'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Get reservation by code.
     */
    public function show(string $bookingCode): JsonResponse
    {
        $booking = Booking::with(['property', 'room'])
            ->where('reservation_code', $bookingCode)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }
}
