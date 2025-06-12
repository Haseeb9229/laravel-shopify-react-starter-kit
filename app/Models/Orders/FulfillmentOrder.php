<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class FulfillmentOrder extends Model
{
    protected $table = 'fulfilment_orders';

    protected $fillable =[ 
        "order_id",
        "fulfillment_order_id",
        "fulfilment_location_id",
        "name",
        "shipment_status",
        "status",
        "tracking_company",
        "tracking_number",
        "tracking_url"
        ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
