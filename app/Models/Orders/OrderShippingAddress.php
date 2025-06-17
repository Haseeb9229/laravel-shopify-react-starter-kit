<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderShippingAddress extends Model
{
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

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
