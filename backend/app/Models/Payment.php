<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'user_id',
        'booking_id',
        'property_id',
        'amount',
        'method', // cash, card, bank_transfer, wallet
        'status', // pending, completed, failed, refunded
        'transaction_id',
        'reference_number',
        'payment_date',
        'paid_at',
        'refunded_at',
        'refund_reason',
        'notes',
        'receipt_url',
        'gateway_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'array'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    // Accessors
    public function getPaymentNumberAttribute()
    {
        if (!isset($this->attributes['payment_number'])) {
            $this->attributes['payment_number'] = 'PAY-' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
        }
        return $this->attributes['payment_number'];
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'refunded' => 'مسترجع'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getMethodTextAttribute()
    {
        $methods = [
            'cash' => 'نقدي',
            'card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية'
        ];
        return $methods[$this->method] ?? $this->method;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' جنيه';
    }

    // Methods
    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason
        ]);
    }

    public function markAsRefunded($reason = null)
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason
        ]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->payment_number = 'PAY-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        });
    }
}
