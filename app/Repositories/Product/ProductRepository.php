<?php
namespace App\Repositories\Product;

use App\Http\Traits\ResponseTrait;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\ProductMedia\ProductMediaRepositoryInterface;
use App\Repositories\ProductVarient\ProductVarientRepositoryInterface;
use App\Models\Products\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;


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
    public function getProductById(int $id)
    {
        $getProductId = $this->model->find($id);
        return $getProductId;
    }
    public function getShopifyProductById(int $id)
    {
        $getProduct = $this->model->where('shopify_product_id', $id)->first();
        return $getProduct;
        // return new ProductResource($getProduct);
    }
    public function create(array $data)
    {
        if (!empty($data)) {

            $varients = $data['variants'];
            unset($data['variants']);

            $medias = $data['media'];
            unset($data['media']);

            $product = $this->model->create($data);

            foreach ($varients as $varient) {
                $varient['product_id'] = $product->id;
                $this->productVarient->create($varient);
            }

            foreach ($medias as $media) {
                $media['product_id'] = $product->id;
                $this->productMedia->create($media);
            }

            return new ProductResource($product);
        } else {
            $this->sendResponse([false, "Product Data not found", $data], 404);
        }

    }
    public function update(int $id, array $data, $shopify = false)
    {
        if ($shopify) {
            $product = $this->getShopifyProductById($id);
            if ($product) {
                $varients = $data['variants'];
                unset($data['variants']);

                $medias = $data['media'];
                unset($data['media']);

                $product->update($data);

                foreach ($varients as $varient) {
                    $varient['product_id'] = $product->id;
                    $this->productVarient->update($varient['shopify_product_Varient_id'], $varient, $shopify);
                }

                foreach ($medias as $media) {
                    $media['product_id'] = $product->id;

                    $this->productMedia->update($media['shopify_product_media_id'], $media, $shopify);
                }
                $this->sendResponse([true, "Product Update Successfully", $this->logData($data)], 200);

                return new ProductResource($product);
                
            } else {
                $this->sendResponse([false, "Updating Product not found", $data], 404);
            }

        } else {
            $products = $this->getProductById($id);
            foreach ($products as $product) {
                $product->update($data);
                return new ProductResource($product);
            }
        }
    }
    public function delete(int $id)
    {
        $product = $this->getShopifyProductById($id);
        if ($product) {
            $this->productMedia->delete($product->id);
            $this->productVarient->delete($product->id);
            return $product->delete();
        } else {
            Log::error("Product in Repository Not Found for Deletion");
        }
    }
}

