<?php

namespace App\Models\Products;

use App\Models\Orders\LineItemOrder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products\Product;

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
    public function LineItmeOrders(){
        return $this->hasOne(LineItemOrder::class);
    } 
}
