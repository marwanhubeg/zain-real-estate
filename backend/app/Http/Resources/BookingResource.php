<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'type' => $this->type,
            'type_text' => $this->type_text,
            'status' => $this->status,
            'status_text' => $this->status_text,
            
            // مواعيد الحجز
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking_time?->format('H:i'),
            'formatted_datetime' => $this->formatted_datetime,
            'duration_minutes' => $this->duration_minutes,
            'number_of_people' => $this->number_of_people,
            
            // التفاصيل
            'notes' => $this->notes,
            'special_requests' => $this->special_requests,
            
            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            'agent' => new UserResource($this->whenLoaded('agent')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            
            // معلومات الإلغاء
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'cancelled_by' => new UserResource($this->whenLoaded('cancelledBy')),
            
            // معلومات التأكيد
            'confirmed_at' => $this->confirmed_at?->format('Y-m-d H:i:s'),
            'confirmed_by' => new UserResource($this->whenLoaded('confirmedBy')),
            
            // معلومات الإكمال
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            
            // التقييم
            'feedback_rating' => $this->feedback_rating,
            'feedback_comment' => $this->feedback_comment,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
