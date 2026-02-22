<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'icon',
        'category', // interior, exterior, security, utilities
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // العلاقات
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'amenity_property')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getCategoryTextAttribute()
    {
        $categories = [
            'interior' => 'داخلي',
            'exterior' => 'خارجي',
            'security' => 'أمني',
            'utilities' => 'خدمات'
        ];
        return $categories[$this->category] ?? $this->category;
    }

    public function getIconAttribute($value)
    {
        return $value ?? 'fas fa-check-circle';
    }
}
