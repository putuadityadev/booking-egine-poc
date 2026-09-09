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
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'guest_name' => 'required|string|max:150',
            'guest_email' => 'required|email|max:150',
            'guest_phone' => 'nullable|string|max:50',
            'is_member' => 'boolean',
            'member_id' => 'nullable|string',
            'member_tier' => 'nullable|string',
        ]);

        $property = Property::with('membershipProperty')->findOrFail($validated['property_id']);
        $room = Room::where('property_id', $property->id)->findOrFail($validated['room_id']);

        // Calculate nights
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        // Base total calculation
        $nightlyBase = (float) $room->base_price;
        $totalBase = $nightlyBase * $nights;

        // Loyalty discount calculation
        $isMember = !empty($validated['is_member']) && !empty($validated['member_id']);
        $memberTier = $validated['member_tier'] ?? null;
        $discountAmount = 0;

        if ($isMember && $room->is_member_rate_applicable) {
            $discountData = $room->getDiscountedPrice($memberTier);
            if ($discountData['has_discount']) {
                $discountAmount = $discountData['discount_amount'] * $nights;
            }
        }

        $beforeTaxService = max(0, $totalBase - $discountAmount);

        // Tax 11% & Service Charge 10%
        $taxRate = 11;
        $serviceRate = 10;
        $taxValue = round(($beforeTaxService * $taxRate) / 100);
        $serviceValue = round(($beforeTaxService * $serviceRate) / 100);
        $afterTaxService = $beforeTaxService + $taxValue + $serviceValue;

        // Generate reservation reference code
        $reservationCode = 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Split guest name
        $nameParts = explode(' ', trim($validated['guest_name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $firstName;

        // Transaction response from membership API
        $pointsEarned = 0;
        $trxResponse = null;

        // Push transaction to Membership Platform if booking is made by an active member
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
                    'before_tax_service' => $beforeTaxService,
                    'after_tax_service' => $afterTaxService,
                    'price_type' => 'taxservice',
                    'tax_charge' => $taxRate,
                    'service_charge' => $serviceRate,
                    'tax_value' => $taxValue,
                    'service_value' => $serviceValue,
                    'detail' => [
                        [
                            'code' => "{$room->code}-{$reservationCode}",
                            'name' => "{$room->name} ({$nights} Nights)",
                            'qty' => $nights,
                            'subtotal' => $beforeTaxService,
                            'product_id' => $room->code,
                        ],
                    ],
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

                // Extract points earned
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

        // Save local reservation record
        $booking = Booking::create([
            'reservation_code' => $reservationCode,
            'property_id' => $property->id,
            'room_id' => $room->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'nights' => $nights,
            'guests' => $validated['guests'],
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'] ?? null,
            'base_total' => $totalBase,
            'discount_amount' => $discountAmount,
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

        return response()->json([
            'success' => true,
            'message' => 'Reservation confirmed successfully!',
            'data' => [
                'booking_id' => $booking->id,
                'reservation_code' => $booking->reservation_code,
                'property_name' => $property->name,
                'room_name' => $room->name,
                'check_in' => $booking->check_in->format('Y-m-d'),
                'check_out' => $booking->check_out->format('Y-m-d'),
                'nights' => $booking->nights,
                'guests' => $booking->guests,
                'guest_name' => $booking->guest_name,
                'guest_email' => $booking->guest_email,
                'pricing' => [
                    'base_rate' => $totalBase,
                    'member_discount' => $discountAmount,
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
