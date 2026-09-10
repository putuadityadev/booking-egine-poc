<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'reservation_code',
        'property_id',
        'room_id',
        'check_in',
        'check_out',
        'nights',
        'guests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'base_total',
        'discount_amount',
        'tax_amount',
        'service_amount',
        'total_amount',
        'is_member',
        'member_id',
        'member_tier',
        'points_earned',
        'point_release_mode',
        'is_points_materialized',
        'materialized_at',
        'membership_transaction_id',
        'status',
        'booking_items',
        'special_requests',
        'transaction_response',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'nights' => 'integer',
        'guests' => 'integer',
        'base_total' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'service_amount' => 'float',
        'total_amount' => 'float',
        'is_member' => 'boolean',
        'is_points_materialized' => 'boolean',
        'materialized_at' => 'datetime',
        'points_earned' => 'integer',
        'booking_items' => 'array',
        'transaction_response' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
