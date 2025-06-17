<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    protected $fillable = [
        "shopify_order_fulfillment_id",
        "shopify_order_fulfillment_location_id",
        "order_id",
        "name",
        "service",
        "shipment_status",
        "status",
        "tracking_company",
        "tracking_number",
        "tracking_url",
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
