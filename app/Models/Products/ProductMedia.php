<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $table = 'product_media';

    protected $fillable = [
        'shopify_product_media_id',
        'product_id',
        'position',
        'media_content_type',
        'src',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
