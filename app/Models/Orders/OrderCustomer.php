<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderCustomer extends Model
{
    protected $fillable = [
        "shopify_customer_id",
        "email" ,
        "first_name",
        "last_name",
        "phone"
    ];
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
