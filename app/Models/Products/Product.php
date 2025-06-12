<?php

namespace App\Models\Products;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $fillable = [
        'user_id',
        'shopify_product_id',
        'title',
        'handle',
        'description',
        'tags',
        'vendor',
        'product_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function productVarient()
    {
        return $this->hasOne(ProductVarient::class);
    }
    public function productMedia()
    {
        return $this->hasOne(ProductMedia::class);
    }
}
