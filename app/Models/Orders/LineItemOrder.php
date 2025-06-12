<?php

namespace App\Models\Orders;

use App\Models\Products\ProductVarient;
use Illuminate\Database\Eloquent\Model;

class LineItemOrder extends Model
{
    protected $table = 'line_item_orders';

    protected $fillable = [
            "shopify_lineitem_id",
            "order_id",
            "variant_id" ,
            "name" ,
            "price" ,
            "quantity",
            "title"
    ];

    public function Order(){
        return $this->belongsTo(Order::class);
    }

    public function ProductVarient(){
        return $this->belongsTo(ProductVarient::class);
    }
}
