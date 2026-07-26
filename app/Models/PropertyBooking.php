<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyBooking extends Model
{
    protected $fillable = [
        'property_id',
        'booking_id',
        'check_in',
        'check_out',
        'status',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
