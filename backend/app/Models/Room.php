<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'property_id',
        'code',
        'name',
        'description',
        'capacity',
        'bed_type',
        'size_sqm',
        'base_price',
        'image_url',
        'gallery',
        'features',
        'is_member_rate_applicable',
        'tier_discount_rates',
    ];

    protected $casts = [
        'base_price' => 'float',
        'capacity' => 'integer',
        'size_sqm' => 'integer',
        'gallery' => 'array',
        'features' => 'array',
        'is_member_rate_applicable' => 'boolean',
        'tier_discount_rates' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Calculate discounted price for a given member tier name.
     */
    public function getDiscountedPrice(?string $tierName): array
    {
        $base = (float) $this->base_price;

        if (!$this->is_member_rate_applicable || empty($tierName)) {
            return [
                'has_discount' => false,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'final_price' => $base,
            ];
        }

        $rates = $this->tier_discount_rates ?: [
            'Bronze' => 5,
            'Silver' => 10,
            'Gold' => 15,
            'Diamond' => 20,
        ];

        // Case-insensitive lookup
        $normalizedTier = ucfirst(strtolower($tierName));
        $percent = $rates[$normalizedTier] ?? ($rates[$tierName] ?? 10);

        $discountAmount = round(($base * $percent) / 100);
        $finalPrice = max(0, $base - $discountAmount);

        return [
            'has_discount' => true,
            'discount_percent' => $percent,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
        ];
    }
}
