<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'code',
        'name',
        'tagline',
        'description',
        'address',
        'city',
        'country',
        'star_rating',
        'review_score',
        'review_count',
        'badge',
        'image_url',
        'gallery',
        'amenities',
        'has_membership',
    ];

    protected $casts = [
        'gallery' => 'array',
        'amenities' => 'array',
        'has_membership' => 'boolean',
        'review_score' => 'float',
        'review_count' => 'integer',
        'star_rating' => 'integer',
    ];

    public function membershipProperty(): HasOne
    {
        return $this->hasOne(MembershipProperty::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
