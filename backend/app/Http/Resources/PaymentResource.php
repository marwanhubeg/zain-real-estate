<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'amount' => $this->amount,
            'formatted_amount' => $this->formatted_amount,
            'method' => $this->method,
            'method_text' => $this->method_text,
            'status' => $this->status,
            'status_text' => $this->status_text,
            
            // معلومات المعاملة
            'transaction_id' => $this->transaction_id,
            'reference_number' => $this->reference_number,
            
            // التواريخ
            'payment_date' => $this->payment_date?->format('Y-m-d H:i:s'),
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'refunded_at' => $this->refunded_at?->format('Y-m-d H:i:s'),
            
            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'property' => new PropertyResource($this->whenLoaded('property')),
            
            // إيصال الدفع
            'receipt_url' => $this->receipt_url ? asset('storage/' . $this->receipt_url) : null,
            
            // ملاحظات
            'notes' => $this->notes,
            'refund_reason' => $this->refund_reason,
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
