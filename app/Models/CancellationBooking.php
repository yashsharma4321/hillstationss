<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancellationBooking extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cancellation_bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'cancellation_rule_id',
        'amount',
        'deduction_percentage',
        'deduction_amount',
        'refund_amount',
        'reason',
        'cancelled_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_percentage' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the booking that was cancelled.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the cancellation rule that was applied.
     */
    public function cancellationRule(): BelongsTo
    {
        return $this->belongsTo(CancellationRule::class);
    }

    /**
     * Get the refund status.
     */
    public function getRefundStatusAttribute(): string
    {
        if ($this->refund_amount == 0) {
            return 'No Refund';
        } elseif ($this->refund_amount == $this->amount) {
            return 'Full Refund';
        } else {
            return 'Partial Refund';
        }
    }

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted refund amount.
     */
    public function getFormattedRefundAttribute(): string
    {
        return '₹' . number_format($this->refund_amount, 2);
    }

    /**
     * Get the formatted deduction amount.
     */
    public function getFormattedDeductionAttribute(): string
    {
        return '₹' . number_format($this->deduction_amount, 2);
    }

    /**
     * Scope a query to get cancellations with refund.
     */
    public function scopeWithRefund($query)
    {
        return $query->where('refund_amount', '>', 0);
    }

    /**
     * Scope a query to get cancellations without refund.
     */
    public function scopeWithoutRefund($query)
    {
        return $query->where('refund_amount', 0);
    }

    /**
     * Scope a query to get cancellations by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('cancelled_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to get cancellations by booking.
     */
    public function scopeByBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    /**
     * Scope a query to get cancellations by rule.
     */
    public function scopeByRule($query, $ruleId)
    {
        return $query->where('cancellation_rule_id', $ruleId);
    }

    /**
     * Get the days before check-in when cancelled.
     */
    public function getDaysBeforeCheckinAttribute()
    {
        if ($this->booking && $this->booking->check_in_date) {
            $checkin = \Carbon\Carbon::parse($this->booking->check_in_date);
            $cancelled = \Carbon\Carbon::parse($this->cancelled_at);
            return $checkin->diffInDays($cancelled);
        }
        return null;
    }
}