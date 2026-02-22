<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'user_id',
        'property_id',
        'agent_id',
        'type', // visit, rent, buy
        'status', // pending, confirmed, cancelled, completed, no_show
        'booking_date',
        'booking_time',
        'duration_minutes',
        'number_of_people',
        'notes',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'confirmed_at',
        'confirmed_by',
        'completed_at',
        'reminder_sent_at',
        'feedback_rating',
        'feedback_comment'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'duration_minutes' => 'integer',
        'number_of_people' => 'integer',
        'feedback_rating' => 'integer'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', today())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    // Accessors
    public function getBookingNumberAttribute()
    {
        if (!isset($this->attributes['booking_number'])) {
            $this->attributes['booking_number'] = 'BOK-' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
        }
        return $this->attributes['booking_number'];
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'completed' => 'مكتمل',
            'no_show' => 'لم يحضر'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'visit' => 'زيارة',
            'rent' => 'إيجار',
            'buy' => 'شراء'
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->booking_date->format('Y-m-d') . ' ' . $this->booking_time->format('H:i');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_number = 'BOK-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        });
    }
}
