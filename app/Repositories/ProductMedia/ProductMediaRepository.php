<?php
namespace App\Repositories\ProductMedia;

use App\Http\Resources\ProductMediaResource;
use App\Http\Traits\ResponseTrait;
use App\Repositories\ProductMedia\ProductMediaRepositoryInterface;
use App\Models\Products\ProductMedia;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;


class ProductMediaRepository implements ProductMediaRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(ProductMedia $productMedia)
    {
        $this->model = $productMedia;
    }
    public function getProductById(int $id)
    {
        $getProduct = $this->model->where("product_id", $id)->get();
        return $getProduct;
    }
    public function getShopifyProductById(int $id)
    {
        $getProduct = $this->model->where('shopify_product_media_id', $id)->first();
        return $getProduct;
        // return new ProductMediaResource($getProduct);
    }
    public function create(array $data)
    {
        $Productmedia = $this->model->create($data);
        return new ProductMediaResource($Productmedia);
    }
    public function update(int $id, array $data, $shopify = true)
    {
        $updateproduct = $this->getShopifyProductById($id);
        if ($shopify) { 
            $updateproduct->updateOrCreate($data);
            return new ProductMediaResource($updateproduct);
        } 
        else{
            $productMedias = $this->getProductById($id);
            foreach($productMedias as $media){
                $media->update($data);
                return new ProductMediaResource($productMedias);
            }
        }
    }
    public function delete(int $id)
    {
        $medias = $this->getProductById($id);
        if($medias){
            foreach($medias as $media){
                return $media->delete();
            }
        }
        else{
            Log::error("Product in Repository Media Not Found");
        }
    }
}

