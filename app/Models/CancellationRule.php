<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationRule extends Model
{
    protected $fillable = [
        'property_id',
        'days_before',
        'deduction_percentage'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
