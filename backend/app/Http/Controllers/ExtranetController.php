<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\MembershipProperty;
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
            $membershipProperty->merchant_id = $membershipData['merchant_id'] ?? $membershipProperty->merchant_id;
            $membershipProperty->x_tenant_domain = $membershipData['x_tenant_domain'] ?? $membershipProperty->x_tenant_domain;
            $membershipProperty->is_active = $membershipData['is_active'] ?? true;
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
                $merchantId ?? ''
            );

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
            MembershipProperty::create([
                'property_id' => $property->id,
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'] ?? '',
                'merchant_id' => $validated['merchant_id'] ?? '',
                'x_tenant_domain' => $validated['tenant_domain'] ?? 'jeevawasa.localhost',
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully.',
            'data' => $property->load('membershipProperty'),
        ], 201);
    }
}
