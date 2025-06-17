<?php

namespace App\Models\Orders;

use App\Models\Products\ProductVarient;
use Illuminate\Database\Eloquent\Model;

class OrderLineItem extends Model
{
    protected $fillable = [
        'shopify_order_lineitem_id',
        'order_id',
        'price',
        'quantity',
        'sku',
        'title',
        'total_discount',
        'shopify_product_variant_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVarient::class, 'shopify_product_variant_id', 'shopify_product_variant_id');
    }
}
