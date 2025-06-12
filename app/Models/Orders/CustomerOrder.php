<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $table = 'customer_orders';

    protected $fillable = [
        "shopify_customer_id",
        "order_id",
        "email" ,
        "first_name",
        "last_name",
        "phone"
    ];
    public function Order(){
        return $this->belongsTo(Order::class);
    }
}
