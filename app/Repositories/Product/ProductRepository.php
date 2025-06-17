<?php
namespace App\Repositories\Product;

use App\Models\Products\Product;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\ProductMedia\ProductMediaRepositoryInterface;
use App\Repositories\ProductVarient\ProductVarientRepositoryInterface;


class ProductRepository implements ProductRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    protected $productVarient;
    protected $productMedia;
    public function __construct(Product $product, ProductVarientRepositoryInterface $productVarient, ProductMediaRepositoryInterface $productMedia)
    {
        $this->model = $product;
        $this->productVarient = $productVarient;
        $this->productMedia = $productMedia;
    }
    public function getById(int $id)
    {
        $product = $this->model->find($id);
        return $product;
    }
    public function getByShopifyId(int $id)
    {
        $product = $this->model->where('shopify_product_id', $id)->first();
        return $product;
    }
    public function getByUserId(int $id)
    {
        $products = $this->model->where('user_id', $id)->get();
        return $products;
    }
    public function updateOrCreate(array $data)
    {
        $varients = $data['variants'];
        unset($data['variants']);

        $medias = $data['media'];
        unset($data['media']);

        $product = $this->model->updateOrCreate($data);

        foreach ($varients as $varient) {
            $varient['product_id'] = $product->id;
            $this->productVarient->updateOrCreate($varient);
        }

        foreach ($medias as $media) {
            $media['product_id'] = $product->id;
            $this->productMedia->updateOrCreate($media);
        }

        return $product;
    }
    public function delete(int $id)
    {
        $product = $this->getById($id);
        $variants = $this->productVarient->getByProductId($product->id);
        $medias = $this->productMedia->getByProductId($product->id);
        foreach ($variants as $variant) {
            $this->productVarient->delete($variant->id);
        }
        foreach ($medias as $media) {
            $this->productMedia->delete($media->id);
        }
        $product->delete();
    }
}

