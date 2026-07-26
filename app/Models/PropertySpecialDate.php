<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertySpecialDate extends Model
{
    protected $fillable = [
        'property_id',
        'date',
        'amount',
        'label',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
