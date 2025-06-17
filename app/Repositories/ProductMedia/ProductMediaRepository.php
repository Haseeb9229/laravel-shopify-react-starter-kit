<?php
namespace App\Repositories\ProductMedia;

use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\Products\ProductMedia;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductMediaResource;
use App\Repositories\ProductMedia\ProductMediaRepositoryInterface;


class ProductMediaRepository implements ProductMediaRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(ProductMedia $productMedia)
    {
        $this->model = $productMedia;
    }
    public function getById(int $id)
    {
        $media = $this->model->find($id);
        return $media;
    }
    public function getByShopifyId(int $id)
    {
        $media = $this->model->where('shopify_product_media_id', $id)->first();
        return $media;
    }
    public function getByProductId(int $id)
    {
        $media = $this->model->where('product_id', $id)->get();
        return $media;
    }
    public function updateOrCreate(array $data)
    {
        $media = $this->model->updateOrCreate($data);
        return $media;
    }
    public function delete(int $id)
    {
        $media = $this->getById($id);
        $media->delete();
    }
}

