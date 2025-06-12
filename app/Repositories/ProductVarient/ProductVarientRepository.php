<?php 
namespace App\Repositories\ProductVarient;

use App\Http\Resources\ProductVarientResource;
use App\Http\Traits\ResponseTrait;
use App\Repositories\ProductVarient\ProductVarientRepositoryInterface;
use App\Models\Products\ProductVarient;
use App\Http\Resources\ProductResource;
use DB;
use Log;


class ProductVarientRepository implements ProductVarientRepositoryInterface
{
    use ResponseTrait;
    protected $model;

    public function __construct(ProductVarient $ProductVarient)
    {
        $this->model = $ProductVarient;
    }
    public function getProductById(int $id)
    {
        $getProductId = $this->model->where("product_id", $id)->get();
        return $getProductId;
    }
    public function getShopifyProductById(int $id)
    {
        $getProduct = $this->model->where('shopify_product_Varient_id', $id)->first();
        return $getProduct;
        // return new ProductVarientResource($getProduct);
    }
    public function create(array $data)
    {
        $ProductVarientCreated = $this->model->create($data);
        return new ProductVarientResource($ProductVarientCreated);
    }
    public function update(int $id, array $data , $shopify = true)
    {
        $updateproduct = $this->getShopifyProductById($id);
        if($shopify){
            if($updateproduct){
                $updateproduct->update($data); 
                $this->sendResponse([true , "Product Varient Update Successfully" , $this->logData($data)  ] , 200);
                return new ProductVarientResource($updateproduct);
            }
            else
            {
                $this->sendResponse([false , "Updating Product not found of Product Varient" , $data ] , 404);
                return new ProductVarientResource($updateproduct);
            }
        }
        else{
            $productVarients = $this->getProductById($id);
            foreach($productVarients as $varient){
                $varient->update($data);
                return new ProductVarientResource($varient);
            }
        }
        
    }
    public function delete(int $id)
    {
        $Varients = $this->getProductById($id);  
        if($Varients){
            foreach($Varients as $varient){
                return $varient->delete();
            } 
        } else{
            Log::error("Product Varient in Repository Not Found for Deletion");
        }
    }
}

