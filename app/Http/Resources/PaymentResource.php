<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subscription_id' => $this->subscription_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at,
        ];
    }
}
