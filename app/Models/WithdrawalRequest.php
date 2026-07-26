<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = ['vendor_id', 'amount', 'status', 'admin_notes', 'bank_details'];

    protected $casts = [
        'bank_details' => 'array',
        'amount' => 'float',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
