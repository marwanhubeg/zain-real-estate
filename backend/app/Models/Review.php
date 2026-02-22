<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'booking_id',
        'rating',
        'title_ar',
        'title_en',
        'comment_ar',
        'comment_en',
        'pros_ar',
        'pros_en',
        'cons_ar',
        'cons_en',
        'is_verified',
        'is_approved',
        'approved_at',
        'approved_by',
        'helpful_count',
        'images'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'helpful_count' => 'integer',
        'images' => 'array'
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

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeHighRating($query, $min = 4)
    {
        return $query->where('rating', '>=', $min);
    }

    // Accessors
    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getCommentAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->comment_ar : $this->comment_en;
    }

    public function getProsAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->pros_ar : $this->pros_en;
    }

    public function getConsAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->cons_ar : $this->cons_en;
    }

    public function getRatingStarsAttribute()
    {
        $fullStars = floor($this->rating);
        $halfStar = $this->rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars
        ];
    }

    public function getImagesAttribute($value)
    {
        $images = json_decode($value, true) ?? [];
        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $images);
    }

    // Methods
    public function markAsVerified()
    {
        $this->update(['is_verified' => true]);
    }

    public function markAsApproved($userId)
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $userId
        ]);
    }
}
