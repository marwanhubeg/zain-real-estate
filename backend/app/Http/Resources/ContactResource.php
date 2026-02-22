<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'subject' => $this->subject,
            'message' => $this->message,
            
            // التصنيف
            'type' => $this->type,
            'type_text' => $this->type_text,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'priority' => $this->priority,
            'priority_text' => $this->priority_text,
            
            // معلومات تقنية
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            
            // العلاقات
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            
            // الرد
            'reply_message' => $this->reply_message,
            'replied_at' => $this->replied_at?->format('Y-m-d H:i:s'),
            'replied_by' => new UserResource($this->whenLoaded('repliedBy')),
            
            // الإغلاق
            'closed_at' => $this->closed_at?->format('Y-m-d H:i:s'),
            'closed_by' => new UserResource($this->whenLoaded('closedBy')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
