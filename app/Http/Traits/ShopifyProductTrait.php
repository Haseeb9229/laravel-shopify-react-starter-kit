<?php

namespace App\Http\Traits;
use Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Repositories\Product\ProductRepositoryInterface;



trait ShopifyProductTrait
{
    protected $product;
    public function getProductRepository(ProductRepositoryInterface $product)
    {
        $this->product = $product;
    }
    public function getProductsFromShopify(User $user)
    {
        try {
            $productCount = $this->getProductsCountFromShopify($user);
            $cursor = 'null';
            $loop = ceil($productCount / 250);
            $hasErrors = false;
            for ($i = 1; $i <= $loop; $i++) {
                [$products, $nextCursor] = $this->shopifyGraphqlProductQuery($user, $cursor);
                if ($products && $nextCursor) {
                    $cursor = '"' . $nextCursor . '"';
                    foreach ($products as $product) {
                        $product = $this->transformShopifyProductData($product);
                        if (!$this->storeData($this->arrayToObject($product), $user)) {
                            $hasErrors = true;
                        }

                    }
                }
            }
            if($hasErrors) {
                throw new \Exception("Some products could not be stored.");
            }
        } catch (\Exception $e) {
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        return true;
    }
    public function getProductsCountFromShopify($user)
    {
        $query = <<<QUERY
            query{
                productsCount{
                    count
                }
            }
        QUERY;
        $result = $this->arrayToObject($user->api()->graph($query));
        if ($result->errors) {
            return 0;
        } else {
            return $result->body->data->productsCount->count;
        }
    }
    public function shopifyGraphqlProductQuery($user, $cursor)
    {
        $query = <<<QUERY
            query {
                products(first: 250, after: $cursor) {
                    edges {
                        node {
                            id
                            title
                            handle
                            descriptionHtml
                            tags
                            vendor
                            productType
                            status
                            variants(first: 250) {
                                edges {
                                    node {
                                        id
                                        inventoryItem{
                                            id
                                        }
                                        title
                                        sku
                                        price
                                        inventoryQuantity
                                        compareAtPrice
                                    }
                                }
                            }
                            media(first: 250) {
                                edges {
                                    node {
                                        ... on MediaImage {
                                            id
                                            image {
                                                url
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    pageInfo {
                        hasNextPage
                        endCursor
                    }
                }
            }
        QUERY;
        $result = $this->arrayToObject($user->api()->graph($query));
        if ($result->errors) {
            return [null, null];
        } else {
            $products = $result->body->data->products->edges;
            $cursor = $result->body->data->products->pageInfo->endCursor;
            return [$products, $cursor];
        }
    }
    public function storeData($product, User $user)
    {
        DB::beginTransaction();
        try {
            $formatedData = $this->formateProductdata($product, $user);
            $this->product->updateOrCreate($formatedData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to store product: " . json_encode($product));
            Log::error("Exception: " . json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function formateProductdata($product, $user)
    {
        $formatedProduct = [
            'user_id' => $user->id,
            'shopify_product_id' => $product->id,
            'title' => $product->title,
            "handle" => $product->handle,
            'body_html' => $product->body_html,
            'tags' => $product->tags,
            'vendor' => $product->vendor,
            'product_type' => $product->product_type,
            'status' => $product->status,
            'variants' => $this->formateProductvarientData($product->variants),
            'media' => $this->formateProductMedia($product->media)
        ];
        return $formatedProduct;
    }
    public function formateProductvarientData($variants)
    {
        $productVarients = [];
        foreach ($variants as $varient) {
            $productVarients[] = [
                "shopify_product_Varient_id" => $varient->id,
                'shopify_inventory_item_id' => $varient->inventory_item_id,
                'title' => $varient->title,
                'sku' => $varient->sku,
                'price' => $varient->price,
                'inventory_quantity' => $varient->inventory_quantity,
                'compare_at_price' => $varient->compare_at_price
            ];
        }
        return $productVarients;
    }
    public function formateProductMedia($media)
    {
        $productMedia = [];
        foreach ($media as $image) {
            $productMedia[] = [
                'shopify_product_media_id' => $image->id,
                'position' => $image->position ?? null,
                'src' => $image->preview_image->src
            ];
        }
        return $productMedia;
    }
    public function deleteProduct($productId)
    {
        DB::beginTransaction();
        try {
            $product = $this->product->getByShopifyId($productId);
            $this->product->delete($product->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function transformShopifyProductData($data): array
    {
        $node = $data->node;
        $productVariants = [];
        if (!empty($node->variants->edges)) {
            foreach ($node->variants->edges as $edge) {
                $variant = $edge->node;
                $productVariants[] = [
                    'compare_at_price' => $variant->compareAtPrice ?? null,
                    'id' => $this->extractId($variant->id),
                    'price' => $variant->price ?? null,
                    'sku' => $variant->sku ?? null,
                    'title' => $variant->title ?? null,
                    'inventory_item_id' => $this->extractId($variant->inventoryItem->id ?? null),
                    'inventory_quantity' => $variant->inventoryQuantity ?? 0,
                ];
            }
        }
        $productMedia = [];
        if (!empty($node->media->edges)) {
            foreach ($node->media->edges as $index => $edge) {
                $media = $edge->node;
                if ($media) {
                    $productMedia[] = [
                        'id' => $this->extractId($media->id),
                        'position' => $index + 1,
                        'preview_image' => [
                            'src' => $media->image->url ?? null,
                        ],
                    ];
                }
            }
        }
        $product = [
            'body_html' => $node->descriptionHtml,
            'handle' => $node->handle,
            'id' => $this->extractId($node->id),
            'product_type' => $node->productType,
            'title' => $node->title,
            'vendor' => $node->vendor,
            'status' => strtolower($node->status),
            'tags' => $this->arrayToString($node->tags),
            'variants' => $productVariants,
            'media' => $productMedia,
        ];
        return $product;
    }
    public function arrayToObject($data)
    {
        return json_decode(json_encode($data));
    }
    public function arrayToString($data)
    {
        if (is_array($data)) {
            if (empty($data)) {
                return '';
            } else {
                return implode(',', $data);
            }
        }
        return $data;
    }
    public function extractId($id)
    {
        $arr = explode('/', $id);
        return end($arr);
    }
}
