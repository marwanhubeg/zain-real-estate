<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title' => $this->title,
            'slug' => $this->slug,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'description' => $this->description,
            
            // السعر والمساحة
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'area' => $this->area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'floor' => $this->floor,
            
            // النوع والحالة
            'type' => $this->type,
            'type_text' => $this->type_text,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'finishing_type' => $this->finishing_type,
            'finishing_type_text' => $this->finishing_type_text,
            
            // التصنيف والموقع
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location_id' => $this->location_id,
            'location' => new LocationResource($this->whenLoaded('location')),
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'address' => $this->address,
            
            // الإحداثيات
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            
            // خيارات الدفع
            'payment_method' => $this->payment_method,
            'installment_years' => $this->installment_years,
            'down_payment' => $this->down_payment,
            'monthly_payment' => $this->monthly_payment,
            
            // الوسائط
            'main_image' => $this->main_image,
            'gallery' => $this->gallery,
            'video_url' => $this->video_url,
            'virtual_tour_url' => $this->virtual_tour_url,
            
            // المزايا
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
            
            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            
            // ✅ تعطيل التقييمات مؤقتاً
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count,
            'reviews_avg_rating' => 0, // قيمة افتراضية
            'reviews_count' => 0, // قيمة افتراضية
            
            // SEO
            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_title' => $this->meta_title,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'meta_description' => $this->meta_description,
            
            // التواريخ
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            
            // معلومات الاعتماد
            'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
        ];
    }
}
