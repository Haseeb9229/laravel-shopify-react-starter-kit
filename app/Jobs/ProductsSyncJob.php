<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Traits\ShopifyProductTrait;
use Log;
use stdClass;
use App\Http\Traits\ResponseTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use App\Repositories\Product\ProductRepositoryInterface;

class ProductsSyncJob implements ShouldQueue
{
     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseTrait , ShopifyProductTrait;

    /**
     * Create a new job instance.
     *  @var ShopDomain|string
     */
     /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    
    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    
    
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }
    public function handle(IShopQuery $shopQuery): void
    {
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $shopQuery->getByDomain($this->shopDomain);
        $user = User::where('name', $shop->name)->first();
        $payload = $this->data;   
        // $this->getProductRepository(app(ProductRepositoryInterface::class));
        
        // if($this->StoreDataToDatabase($payload , $user , false)){
        //     log::info("Product sync Job Successfully");
        // }else{
        //     Log::error("Product sync Job Failed");
        // }
    }
}
