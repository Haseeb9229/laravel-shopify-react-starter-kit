<?php

namespace App\Http\Traits;
use App\Http\Resources\ProductResource;
use App\Models\User;
use App\Repositories\Product\ProductRepositoryInterface;
use Log;
use Illuminate\Support\Facades\DB;



trait ShopifyProductTrait
{
    protected $product;
    public function getProductRepository(ProductRepositoryInterface $product)
    {
        $this->product = $product;
    }
    public function formateProductdata($product, $user)
    {
        $products = [
            'user_id' => $user->id,
            'shopify_product_id' => $product->id,
            'title' => $product->title,
            "handle" => $product->handle,
            'description' => $product->body_html,
            'tags' => $product->tags,
            'vendor' => $product->vendor,
            'product_type' => $product->product_type,
            'status' => $product->status,
            "media" => $this->formateProductMedia($product->media),
            'variants' => $this->formateProductvarientData($product->variants) //adding varients in the products to store in the database
        ];
        return $products;
    }
    public function formateProductvarientData($variants)
    {
        $productVarient = [];
        foreach ($variants as $varient) {
            $productVarient[] = [
                "shopify_product_Varient_id" => $varient->id,
                'product_id' => $varient->product_id,
                'shopify_inventory_item_id' => $varient->inventory_item_id,
                'title' => $varient->title,
                'sku' => $varient->sku,
                'price' => $varient->price,
                'inventory_quantity' => $varient->inventory_quantity,
                'compare_at_price' => $varient->compare_at_price
            ];
        }
        return $productVarient;
    }
    public function formateProductMedia($medias)
    {
        $productMedia = [];
        foreach ($medias as $media) {
            $productMedia[] = [
                'shopify_product_media_id' => $media->id,
                'product_id' => $media->product_id,
                'position' => $media->position,
                'media_content_type' => $media->media_content_type,
                'src' => $media->preview_image->src
            ];
        }
        return $productMedia;
    }
    public function storeData($productId, User $user, $update = false)
    {
        $formattedData = $this->formateProductdata($productId, $user);
        if ($update) {
            $this->product->update($formattedData['shopify_product_id'], $formattedData, true);
            Log::info("Product Updated Successfully from traits!!");
        } else {
            $this->product->create($formattedData);
            Log::info("Product Created Successfully from traits!!");
        }
    }
    public function StoreDataToDatabase($product, $user, $shopifyUpdate = false)
    {
       DB::beginTransaction();
        try {
            $this->storeData($product, $user, $shopifyUpdate);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function DeleteProduct($prouductId)
    {   
        DB::beginTransaction();
        try {
            $this->logData($prouductId);
            $this->product->delete($prouductId->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
}
