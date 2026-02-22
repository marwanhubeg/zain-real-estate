<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_ar' => $this->city_ar,
            'city_en' => $this->city_en,
            'city' => $this->city,
            'district_ar' => $this->district_ar,
            'district_en' => $this->district_en,
            'district' => $this->district,
            'full_address' => $this->full_address,
            
            // الإحداثيات
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zoom_level' => $this->zoom_level,
            'google_maps_url' => $this->google_maps_url,
            
            // الحالة
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            
            // العلاقات
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),
            'properties_count' => $this->when(isset($this->properties_count), $this->properties_count),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
