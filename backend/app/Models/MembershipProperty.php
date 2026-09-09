<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipProperty extends Model
{
    protected $fillable = [
        'property_id',
        'x_tenant_domain',
        'client_id',
        'client_secret',
        'merchant_id',
        'corporate_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'client_secret',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
