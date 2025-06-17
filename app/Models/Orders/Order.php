<?php

namespace App\Models\Orders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $fillable = [
        'shopify_order_id',
        'user_id',
        'contact_email',
        'email',
        'financial_status',
        'fulfillment_status',
        'name',
        'note',
        'phone',
        'subtotal_price',
        'tags',
        'total_discounts',
        'total_line_items_price',
        'total_outstanding',
        'total_price',
        'total_shipping_price',
        'total_tax',
        'total_tip_received',
        'total_weight',
        'order_customer_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderLineItems()
    {
        return $this->hasMany(OrderLineItem::class);
    }
    public function orderCustomer()
    {
        return $this->belongsTo(OrderCustomer::class);
    }
    public function orderFulfillments()
    {
        return $this->hasMany(OrderFulfillment::class);
    }
    public function orderShippingAddress()
    {
        return $this->hasOne(OrderShippingAddress::class);
    }
}
