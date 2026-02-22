<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Property extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'description_ar',
        'description_en',
        'price',
        'area',
        'bedrooms',
        'bathrooms',
        'floor',
        'type', // sale, rent
        'category_id',
        'user_id',
        'location_id',
        'status', // available, pending, sold, rented
        'is_featured',
        'views_count',
        'year_built',
        'finishing_type', // finished, semi-finished, unfinished
        'payment_method', // cash, installment, both
        'installment_years',
        'down_payment',
        'monthly_payment',
        'latitude',
        'longitude',
        'address_ar',
        'address_en',
        'video_url',
        'virtual_tour_url',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
        'expires_at',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title_ar')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_property')
                    ->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function images()
    {
        return $this->getMedia('properties');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeForSale($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('type', 'rent');
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeInIsmailia($query)
    {
        return $query->whereHas('location', function ($q) {
            $q->where('city_ar', 'like', '%الإسماعيلية%')
              ->orWhere('city_en', 'like', '%Ismailia%');
        });
    }

    // Accessors
    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getAddressAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->address_ar : $this->address_en;
    }

    public function getMetaTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->meta_title_ar : $this->meta_title_en;
    }

    public function getMetaDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->meta_description_ar : $this->meta_description_en;
    }

    public function getMainImageAttribute()
    {
        $image = $this->getFirstMedia('properties');
        return $image ? $image->getUrl() : asset('images/default-property.jpg');
    }

    public function getGalleryAttribute()
    {
        return $this->getMedia('properties')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumb'),
                'name' => $media->name,
                'size' => $media->size,
                'is_main' => $media->getCustomProperty('is_main', false)
            ];
        });
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0) . ' جنيه';
    }

    public function getTypeTextAttribute()
    {
        return $this->type == 'sale' ? 'للبيع' : 'للإيجار';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'available' => 'متاح',
            'pending' => 'قيد الانتظار',
            'sold' => 'تم البيع',
            'rented' => 'تم التأجير'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getFinishingTypeTextAttribute()
    {
        $types = [
            'finished' => 'تشطيب كامل',
            'semi-finished' => 'نصف تشطيب',
            'unfinished' => 'غير مشطب'
        ];
        return $types[$this->finishing_type] ?? $this->finishing_type;
    }

    // Mutators
    public function setTitleArAttribute($value)
    {
        $this->attributes['title_ar'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
}
