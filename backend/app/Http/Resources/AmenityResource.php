<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmenityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name,
            'icon' => $this->icon,
            'category' => $this->category,
            'category_text' => $this->category_text,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            
            // العلاقات
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),
            'properties_count' => $this->when(isset($this->properties_count), $this->properties_count),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
