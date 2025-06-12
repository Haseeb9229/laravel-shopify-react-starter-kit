<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Traits\ShopifyOrderTrait;
use Log;
use stdClass;
use App\Http\Traits\ResponseTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use App\Repositories\Order\OrderRepositoryInterface;


class OrdersSyncJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels, ShopifyOrderTrait , ResponseTrait;

    protected $user;
    protected $shopDomain;
    protected $data;

    /**
     * Create a new job instance.
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
     */
    public function handle(IShopQuery $shopQuery): void
    {

        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $shopQuery->getByDomain($this->shopDomain);
        $user = User::where('name', $shop->name)->first();
        $payload = $this->data;
        $this->logData($payload);
        $this->logData($user);

        $this->getOrderRepository(app(OrderRepositoryInterface::class));
        if ($this->StoreDataToDatabase($payload, $user, false)) {
            log::info("Order Sync Job Successfully");
        } else {
            Log::error("Order Sync Job Failed");
        }
    }
}
