<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'rating_stars' => $this->rating_stars,
            
            // العناوين والمحتوى
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'title' => $this->title,
            'comment_ar' => $this->comment_ar,
            'comment_en' => $this->comment_en,
            'comment' => $this->comment,
            
            // الإيجابيات والسلبيات
            'pros_ar' => $this->pros_ar,
            'pros_en' => $this->pros_en,
            'pros' => $this->pros,
            'cons_ar' => $this->cons_ar,
            'cons_en' => $this->cons_en,
            'cons' => $this->cons,
            
            // الصور
            'images' => $this->images,
            
            // الحالة
            'is_verified' => $this->is_verified,
            'is_approved' => $this->is_approved,
            
            // الإحصائيات
            'helpful_count' => $this->helpful_count,
            
            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            
            // معلومات الاعتماد
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
