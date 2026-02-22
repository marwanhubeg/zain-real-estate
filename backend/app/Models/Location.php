<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_ar',
        'city_en',
        'district_ar',
        'district_en',
        'latitude',
        'longitude',
        'zoom_level',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'zoom_level' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // العلاقات
    public function properties()
    {
        return $this->hasMany(Property::class);
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

    public function scopeInIsmailia($query)
    {
        return $query->where('city_ar', 'like', '%الإسماعيلية%')
                     ->orWhere('city_en', 'like', '%Ismailia%');
    }

    // Accessors
    public function getCityAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->city_ar : $this->city_en;
    }

    public function getDistrictAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->district_ar : $this->district_en;
    }

    public function getFullAddressAttribute()
    {
        return $this->district . '، ' . $this->city;
    }

    public function getPropertiesCountAttribute()
    {
        return $this->properties()->count();
    }

    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
