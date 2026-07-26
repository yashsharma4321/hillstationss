<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vendor extends Model
{
    protected $fillable = [
        'user_id', 'business_name', 'amount', 'business_phone', 'business_email',
        'city', 'state', 'address', 'brand_logo',
        'commission_rate', 'is_approved', 'kyc_status', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function bankDetail(): HasOne
    {
        return $this->hasOne(VendorBankDetail::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(VendorWallet::class);
    }
}
