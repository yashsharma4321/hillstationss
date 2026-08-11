<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $fillable = [
        'property_id', 'vendor_id', 'user_id', 'name', 'email', 'phone',
        'check_in', 'check_out', 'total_amount', 'subtotal', 'discount', 'gst',
        'adults', 'children', 'room_type_id', 'coupon_code', 'status', 'message',
    ];

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
