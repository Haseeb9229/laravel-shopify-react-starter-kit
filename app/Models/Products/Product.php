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
        'body_html',
        'tags',
        'vendor',
        'product_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function productVarients()
    {
        return $this->hasMany(ProductVarient::class);
    }
    public function productMedias()
    {
        return $this->hasMany(ProductMedia::class);
    }
}
