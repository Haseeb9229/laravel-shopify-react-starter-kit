<?php
namespace App\Repositories\ProductVarient;

use App\Http\Traits\ResponseTrait;
use App\Models\Products\ProductVarient;
use App\Repositories\ProductVarient\ProductVarientRepositoryInterface;


class ProductVarientRepository implements ProductVarientRepositoryInterface
{
    use ResponseTrait;
    protected $model;

    public function __construct(ProductVarient $ProductVarient)
    {
        $this->model = $ProductVarient;
    }
    public function getById(int $id)
    {
        $variant = $this->model->find($id);
        return $variant;
    }
    public function getByShopifyId(int $id)
    {
        $variant = $this->model->where('shopify_product_Varient_id', $id)->first();
        return $variant;
    }
    public function getByProductId(int $id)
    {
        $variants = $this->model->where('product_id', $id)->get();
        return $variants;
    }
    public function updateOrCreate(array $data)
    {
        $productVarient = $this->model->updateOrCreate($data);
        return $productVarient;
    }
    public function delete(int $id)
    {
        $varient = $this->getById($id);
        $varient->delete();
    }
}

