<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    protected $fillable = [
        'property_id',
        'code',
        'name',
        'category',
        'duration',
        'price',
        'image_url',
        'rating',
        'badge',
        'is_member_rate_applicable',
        'member_perk',
    ];

    protected $casts = [
        'price' => 'float',
        'rating' => 'float',
        'is_member_rate_applicable' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
