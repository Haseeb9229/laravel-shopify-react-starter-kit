<?php

namespace App\Models\Orders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   public $fillable = [
       "shopify_order_id",
       'user_id',
       'contact_email',
       'currency',
       'email',
       'financial_status',
       'fulfillment_status',
       'name',
       'phone',
       'subtotal_price',
       'tags',
       'total_discounts',
       'total_line_items_price',
       'total_outstanding',
       'total_price',
       'total_tax',
       'total_weight',
   ];
   public function user(){
       return $this->belongsTo(User::class);
   }
   public function LineItemOrder(){
       return $this->hasMany(LineItemOrder::class);
   }
   public function CustomerOrder(){
       return $this->hasone(CustomerOrder::class);
   }
   public function FulfillmentOrder(){
       return $this->hasMany(FulfillmentOrder::class);
   }
   public function ShippingAddressOrder()   {
       return $this->hasOne(ShippingAddressOrder::class);
   }
}
