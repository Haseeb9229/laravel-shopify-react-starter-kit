<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class ShippingAddressOrder extends Model
{
    protected $table = 'shipping_address_orders';

    protected $fillable = [
            "order_id" ,
            "first_name" ,
            "last_name" ,
            "address1" ,
            "phone" ,
            "city",
            "zip" ,
            "province" ,
            "country",
            "company" ,
            "country_code" ,
            "province_code"
    ];

    public function Order(){
        return $this->belongsTo(Order::class);
    }
}
