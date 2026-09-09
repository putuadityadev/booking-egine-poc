<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * List all available properties.
     */
    public function index(): JsonResponse
    {
        $properties = Property::with(['membershipProperty'])
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'code' => $property->code,
                    'name' => $property->name,
                    'tagline' => $property->tagline,
                    'city' => $property->city,
                    'country' => $property->country,
                    'star_rating' => $property->star_rating,
                    'review_score' => $property->review_score,
                    'review_count' => $property->review_count,
                    'badge' => $property->badge,
                    'image_url' => $property->image_url,
                    'has_membership' => $property->has_membership,
                    'tenant_domain' => $property->membershipProperty?->x_tenant_domain,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    /**
     * Get detailed property catalog including rooms and experiences.
     */
    public function show(int $id): JsonResponse
    {
        $property = Property::with([
            'membershipProperty',
            'rooms',
            'experiences',
        ])->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $property->id,
                'code' => $property->code,
                'name' => $property->name,
                'tagline' => $property->tagline,
                'description' => $property->description,
                'address' => $property->address,
                'city' => $property->city,
                'country' => $property->country,
                'star_rating' => $property->star_rating,
                'review_score' => $property->review_score,
                'review_count' => $property->review_count,
                'badge' => $property->badge,
                'image_url' => $property->image_url,
                'gallery' => $property->gallery,
                'amenities' => $property->amenities,
                'has_membership' => $property->has_membership,
                'membership' => $property->membershipProperty ? [
                    'tenant_domain' => $property->membershipProperty->x_tenant_domain,
                    'client_id' => $property->membershipProperty->client_id,
                    'merchant_id' => $property->membershipProperty->merchant_id,
                ] : null,
                'rooms' => $property->rooms,
                'experiences' => $property->experiences,
            ],
        ]);
    }
}
