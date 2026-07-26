<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorWallet extends Model
{
    protected $fillable = ['vendor_id', 'balance', 'total_earned'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
