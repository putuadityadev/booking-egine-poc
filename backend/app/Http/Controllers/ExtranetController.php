<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\MembershipProperty;
use App\Models\Room;
use App\Services\MembershipOAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtranetController extends Controller
{
    protected MembershipOAuthService $oauthService;

    public function __construct(MembershipOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    /**
     * List all properties for extranet property management.
     */
    public function index(): JsonResponse
    {
        $properties = Property::with(['membershipProperty', 'rooms'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    /**
     * Get specific property settings for extranet editor.
     */
    public function show(int $id): JsonResponse
    {
        $property = Property::with(['membershipProperty', 'rooms', 'experiences'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    /**
     * Update property information and Membership OAuth credentials.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'image_url' => 'nullable|url',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'has_membership' => 'boolean',
            'membership' => 'nullable|array',
            'membership.client_id' => 'nullable|string',
            'membership.client_secret' => 'nullable|string',
            'membership.merchant_id' => 'nullable|string',
            'membership.x_tenant_domain' => 'nullable|string',
            'membership.is_active' => 'nullable|boolean',
        ]);

        $property->update([
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? $property->tagline,
            'description' => $validated['description'] ?? $property->description,
            'address' => $validated['address'] ?? $property->address,
            'city' => $validated['city'],
            'country' => $validated['country'],
            'image_url' => $validated['image_url'] ?? $property->image_url,
            'star_rating' => $validated['star_rating'] ?? $property->star_rating,
            'has_membership' => $validated['has_membership'] ?? $property->has_membership,
        ]);

        if (!empty($validated['membership'])) {
            $membershipData = $validated['membership'];
            $membershipProperty = $property->membershipProperty ?: new MembershipProperty(['property_id' => $property->id]);
            
            $membershipProperty->client_id = $membershipData['client_id'] ?? $membershipProperty->client_id;
            if (!empty($membershipData['client_secret'])) {
                $membershipProperty->client_secret = $membershipData['client_secret'];
            }
            $membershipProperty->x_tenant_domain = $membershipData['x_tenant_domain'] ?? $membershipProperty->x_tenant_domain;
            $membershipProperty->is_active = $membershipData['is_active'] ?? true;

            $merchantId = $membershipData['merchant_id'] ?? $membershipProperty->merchant_id;
            if (empty($merchantId) && !empty($membershipProperty->client_id) && !empty($membershipProperty->x_tenant_domain)) {
                try {
                    $contextResult = $this->oauthService->testConnection(
                        $membershipProperty->client_id,
                        $membershipProperty->client_secret ?? '',
                        $membershipProperty->x_tenant_domain
                    );
                    $merchantId = $contextResult['merchant_id'] ?? null;
                    if (!empty($contextResult['corporate_id'])) {
                        $membershipProperty->corporate_id = $contextResult['corporate_id'];
                    }
                } catch (\Throwable $ctxErr) {
                    // Retain existing merchant ID if test resolution fails
                }
            }
            $membershipProperty->merchant_id = $merchantId;
            $membershipProperty->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Property settings updated successfully.',
            'data' => $property->fresh(['membershipProperty', 'rooms']),
        ]);
    }

    /**
     * Test live OAuth 2.1 connection and fetch property context from Membership API.
     */
    public function testConnection(Request $request, int $id): JsonResponse
    {
        $property = Property::with('membershipProperty')->findOrFail($id);

        $clientId = $request->input('client_id', $property->membershipProperty?->client_id);
        $clientSecret = $request->input('client_secret', $property->membershipProperty?->client_secret);
        $merchantId = $request->input('merchant_id', $property->membershipProperty?->merchant_id);
        $tenantDomain = $request->input('x_tenant_domain', $property->membershipProperty?->x_tenant_domain);

        if (!$clientId || !$tenantDomain) {
            return response()->json([
                'success' => false,
                'message' => 'Client ID and Tenant Domain are required to test connection.',
            ], 422);
        }

        try {
            $result = $this->oauthService->testConnection(
                $clientId,
                $clientSecret ?? '',
                $tenantDomain,
                $merchantId
            );

            // Automatically persist resolved merchant and corporate IDs
            if ($property->membershipProperty && !empty($result['merchant_id'])) {
                $property->membershipProperty->update([
                    'merchant_id' => $result['merchant_id'],
                    'corporate_id' => $result['corporate_id'] ?? $property->membershipProperty->corporate_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'OAuth 2.1 Connection Healthy! Property Context resolved.',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get loyalty tiers resolved from membership platform.
     */
    public function getTiers(int $id): JsonResponse
    {
        $property = Property::with('membershipProperty')->findOrFail($id);

        $defaultTiers = [
            ['id' => 'bronze', 'name' => 'Bronze', 'transaction_value_min' => 0],
            ['id' => 'silver', 'name' => 'Silver', 'transaction_value_min' => 15000000],
            ['id' => 'gold', 'name' => 'Gold', 'transaction_value_min' => 40000000],
            ['id' => 'diamond', 'name' => 'Diamond', 'transaction_value_min' => 100000000],
        ];

        if (!$property->has_membership || !$property->membershipProperty) {
            return response()->json([
                'success' => true,
                'data' => $defaultTiers,
            ]);
        }

        try {
            $context = $this->oauthService->getPropertyContext($property);
            $tiers = $context['data']['tiers'] ?? [];
            return response()->json([
                'success' => true,
                'data' => !empty($tiers) ? $tiers : $defaultTiers,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'data' => $defaultTiers,
            ]);
        }
    }

    /**
     * Update room rate plan (member rate applicability and tier discount matrix).
     */
    public function updateRoomRatePlan(Request $request, int $id, int $roomId): JsonResponse
    {
        $property = Property::findOrFail($id);
        $room = Room::where('property_id', $property->id)->findOrFail($roomId);

        $validated = $request->validate([
            'is_member_rate_applicable' => 'required|boolean',
            'tier_discount_rates' => 'nullable|array',
        ]);

        $room->update([
            'is_member_rate_applicable' => $validated['is_member_rate_applicable'],
            'tier_discount_rates' => $validated['tier_discount_rates'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room rate plan updated successfully.',
            'data' => $room,
        ]);
    }

    /**
     * Create a new property in extranet.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'tenant_domain' => 'nullable|string',
        ]);

        $code = strtoupper(Str::slug($validated['name']));

        $property = Property::create([
            'code' => $code,
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? 'Luxury Stay',
            'description' => $validated['description'] ?? 'Exclusive destination retreat.',
            'city' => $validated['city'],
            'country' => $validated['country'],
            'image_url' => $validated['image_url'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            'star_rating' => 5,
            'review_score' => 4.95,
            'review_count' => 50,
            'badge' => 'Guest favorite',
            'amenities' => ['Swimming Pool', 'High-Speed Wi-Fi', 'Breakfast Included'],
            'has_membership' => !empty($validated['client_id']),
        ]);

        if (!empty($validated['client_id'])) {
            $merchantId = $validated['merchant_id'] ?? null;
            $corporateId = null;
            $tenantDomain = $validated['tenant_domain'] ?? 'jeevawasa.localhost';

            if (empty($merchantId)) {
                try {
                    $contextResult = $this->oauthService->testConnection(
                        $validated['client_id'],
                        $validated['client_secret'] ?? '',
                        $tenantDomain
                    );
                    $merchantId = $contextResult['merchant_id'] ?? null;
                    $corporateId = $contextResult['corporate_id'] ?? null;
                } catch (\Throwable $err) {
                    // Fallback to empty if resolution fails
                }
            }

            MembershipProperty::create([
                'property_id' => $property->id,
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'] ?? '',
                'merchant_id' => $merchantId ?? '',
                'corporate_id' => $corporateId,
                'x_tenant_domain' => $tenantDomain,
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully.',
            'data' => $property->load(['membershipProperty', 'rooms']),
        ], 201);
    }
}
