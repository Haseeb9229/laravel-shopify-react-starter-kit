<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user_id" => $this->id,
            "shopify_this_id" => $this->id, 
            "contact_email" => $this->name,
            "currency" => $this->currency,
            "email" => $this->email, 
            "financial_status" => $this->financial_status,
            "fulfillment_status" => $this->fulfillment_status,
            "name" => $this->name,
            "phone" => $this->phone,
            "subtotal_price" => $this->subtotal_price,
            "tags" => $this->tags,
            "total_discounts" => $this->total_discounts,
            "total_line_items_price" => $this->total_line_items_price,
            "total_outstanding" => $this->total_outstanding,
            "total_price" => $this->total_price,
            "total_tax" => $this->total_tax,
            "total_weight" => $this->total_weight,
        ];
    }
}
