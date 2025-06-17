<?php

namespace App\Models\Products;

use App\Models\Products\Product;
use App\Models\Orders\OrderLineItem;
use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{
    protected $fillable = [
        'shopify_product_Varient_id',
        'product_id',
        'sku',
        'price',
        'title',
        'shopify_inventory_item_id',
        'compare_at_price',
        'inventory_quantity',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function orderLineItems(){
        return $this->hasMany(OrderLineItem::class, 'shopify_product_variant_id', 'shopify_product_variant_id');
    }
}
