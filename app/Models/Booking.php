<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_number', 
        'customer_id', 
        'vendor_id', 
        'property_id', 
        'room_id', 
        'coupon_id',
        'check_in', 
        'check_out', 
        'guest_count', 
        'adults', 
        'children',
        'total_amount', 
        'discount_amount', 
        'gst_amount', 
        'commission_amount', 
        'vendor_amount',
        'commission_percentage', 
        'final_amount', 
        'status', 
        'payment_status',
        'razorpay_payment_id', 
        'razorpay_order_id', 
        'razorpay_signature',
        // Cancellation fields
        'cancellation_reason',
        'cancellation_date',
        'deduction_percentage',
        'deduction_amount',
        'refund_amount',
        'refund_id',
        'refund_status'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'cancellation_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'deduction_percentage' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function cancellationBooking()
    {
        return $this->hasOne(CancellationBooking::class);
    }

    // Helper methods
    public function isCancellable()
    {
        return $this->status !== 'cancelled' && $this->payment_status === 'paid';
    }

    public function getRefundStatusAttribute($value)
    {
        if ($this->refund_amount == 0) {
            return 'No Refund';
        } elseif ($value === 'completed') {
            return 'Refunded';
        } elseif ($value === 'pending') {
            return 'Refund Pending';
        } elseif ($value === 'failed') {
            return 'Refund Failed';
        }
        return $value ?: 'Processing';
    }

    public function getFormattedRefundAmountAttribute()
    {
        return '₹' . number_format($this->refund_amount, 2);
    }

    public function getFormattedDeductionAmountAttribute()
    {
        return '₹' . number_format($this->deduction_amount, 2);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}