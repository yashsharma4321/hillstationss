<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'vendor_id', 'admin_id', 'amount', 'type', 'category', 'description', 'reference_id', 'balance_after'
    ];
}
