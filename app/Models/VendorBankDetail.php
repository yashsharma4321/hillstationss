<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    protected $fillable = [
        'vendor_id',
        'account_number',
        'bank_name',
        'ifsc_code',
        'upi_id'
    ];
}
