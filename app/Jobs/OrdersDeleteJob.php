<?php namespace App\Jobs;

use stdClass;
use Illuminate\Bus\Queueable;
use App\Http\Traits\ResponseTrait;
use App\Http\Traits\ShopifyOrderTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use App\Repositories\Order\OrderRepositoryInterface;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;

class OrdersDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseTrait , ShopifyOrderTrait;

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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IShopQuery $shopQuery)
    {
        $payload = $this->data;
        $this->getOrderRepository(app(OrderRepositoryInterface::class));
        if($this->deleteOrder($payload->id)){
            $this->logInfo("Order Delete Job Sucessfull.");
        }else{
            $this->logInfo("Order Delete Job Failed! ");
        }
    }
}
