<?php

namespace App\Jobs;

use App\Models\User;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\ShopifyOrderTrait;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Order\OrderRepositoryInterface;

class OrderSyncJob implements ShouldQueue
{
    use Queueable, ShopifyOrderTrait, ResponseTrait;

    /**
     * Create a new job instance.
     */

    protected $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->getOrderRepository(app(OrderRepositoryInterface::class));
        $user = User::find($this->userId);
        if ($this->getOrdersFromShopify($user)) {
            $this->logInfo('Orders Synced successfully from Shopify for user ID: ' . $this->userId);
        } else {
            $this->logInfo('Orders Synced failed from Shopify for user ID: ' . $this->userId);
        }
    }
}
