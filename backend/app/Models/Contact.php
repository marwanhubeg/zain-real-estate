<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'type', // general, support, complaint, suggestion
        'status', // new, read, replied, closed
        'priority', // low, medium, high
        'assigned_to',
        'replied_at',
        'replied_by',
        'reply_message',
        'closed_at',
        'closed_by',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    // العلاقات
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'new' => 'جديد',
            'read' => 'تمت القراءة',
            'replied' => 'تم الرد',
            'closed' => 'مغلق'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getPriorityTextAttribute()
    {
        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع'
        ];
        return $priorities[$this->priority] ?? $this->priority;
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'general' => 'عام',
            'support' => 'دعم',
            'complaint' => 'شكوى',
            'suggestion' => 'اقتراح'
        ];
        return $types[$this->type] ?? $this->type;
    }

    // Methods
    public function markAsRead()
    {
        if ($this->status == 'new') {
            $this->update(['status' => 'read']);
        }
    }

    public function reply($message, $userId)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => $userId,
            'reply_message' => $message
        ]);
    }

    public function close($userId)
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => $userId
        ]);
    }
}
