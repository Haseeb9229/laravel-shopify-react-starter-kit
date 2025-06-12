<?php

namespace App\Jobs;

use App\Http\Traits\ResponseTrait;
use App\Http\Traits\ShopifyOrderTrait;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use stdClass;
use App\Repositories\Order\OrderRepositoryInterface;

class OrdersDeleteJob implements ShouldQueue
{
      use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseTrait , ShopifyOrderTrait;

    /**
     * Shop's myshopify domain
     *
     * @var \Osiset\ShopifyApp\Objects\Values\ShopDomain|string
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IShopQuery $shopQuery)
    {
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $shopQuery->getByDomain($this->shopDomain);
        $user = User::where('name', $shop->name)->first();
        $payload = $this->data;
        
        $this->getOrderRepository(app(OrderRepositoryInterface::class));
       
        if( $this->DeleteOrder($payload)){
             \Log::info("Order Delete Job Runs! ");
        }else{
            \Log::error("Order Delete Job Failed! ");
        }
        

            
       
    }
}
