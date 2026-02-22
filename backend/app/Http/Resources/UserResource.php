<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'avatar' => $this->avatar_url,
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'bio' => $this->bio,
            'role' => $this->role,
            'role_text' => $this->role_text,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'last_login_ip' => $this->last_login_ip,
            
            // معلومات إضافية للوكلاء
            'company_name' => $this->when($this->role === 'agent', $this->company_name),
            'company_registration' => $this->when($this->role === 'agent', $this->company_registration),
            'license_number' => $this->when($this->role === 'agent', $this->license_number),
            'years_of_experience' => $this->when($this->role === 'agent', $this->years_of_experience),
            'specialization' => $this->when($this->role === 'agent', $this->specialization),
            
            // العنوان
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            
            // وسائل التواصل الاجتماعي
            'social' => [
                'facebook' => $this->facebook_url,
                'twitter' => $this->twitter_url,
                'instagram' => $this->instagram_url,
                'linkedin' => $this->linkedin_url,
                'website' => $this->website_url,
            ],
            
            // إحصائيات
            'statistics' => [
                'properties_count' => $this->when($this->role === 'agent', $this->properties_count),
                'approved_properties_count' => $this->when($this->role === 'agent', $this->approved_properties_count),
                'bookings_count' => $this->bookings_count,
                'reviews_count' => $this->reviews_count,
                'favorites_count' => $this->favoriteProperties()->count(),
            ],
            
            // العلاقات
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'favorite_properties' => PropertyResource::collection($this->whenLoaded('favoriteProperties')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
