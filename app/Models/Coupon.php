<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase', 'expires_at', 
        'usage_limit', 'used_count', 'is_active', 'is_global', 'description',
        'vendor_id', 'property_id', 'user_id'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'value' => 'float',
        'min_purchase' => 'float',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }
}
