<?php

namespace App\Jobs;

use stdClass;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Contracts\Commands\Shop;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as QueriesShop;

class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public $tries = 1;

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
    public function handle(Shop $shopCommand, QueriesShop $shopQuery, CancelCurrentPlan $cancelCurrentPlanAction): bool
    {
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);

        $shop = $shopQuery->getByDomain($this->shopDomain);
        $user = User::where('name', $shop->name)->first();
        $products = $user->products;
        foreach ($products as $product) {
            $product->productImages()->delete();
            $products->productVarients()->delete();
            $product->delete();
        }
        $orders = $user->orders;
        foreach ($orders as $order) {
            $orders->orderLineItems()->delete();
            $order->orderFulfillments()->delete();
            $order->orderShippingAddress()->delete();
            $order->orderCustomer()->delete();
            $order->delete();
        }
        $user->delete();
        Log::info('App uninstalled for shop: ' . $this->shopDomain->toNative());
        return true;
    }
}
