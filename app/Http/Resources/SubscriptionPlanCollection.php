<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubscriptionPlanCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function($subscriptionPlan) {
            return [
                'id' => $subscriptionPlan->id,
                'name' => $subscriptionPlan->name,
                'description' => $subscriptionPlan->description,
                'price' => $subscriptionPlan->price,
                'duration' => $subscriptionPlan->duration,
                'created_at' => $subscriptionPlan->created_at,
            ];
        })->toArray();
    }
}
