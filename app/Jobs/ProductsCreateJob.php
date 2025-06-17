<?php namespace App\Jobs;

use stdClass;
use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Http\Traits\ResponseTrait;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\ShopifyProductTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use App\Repositories\Product\ProductRepositoryInterface;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;


class ProductsCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseTrait , ShopifyProductTrait;

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
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $shopQuery->getByDomain($this->shopDomain);
        $user = User::where('name', $shop->name)->first();
        $payload = $this->data;

        $this->getProductRepository(app(ProductRepositoryInterface::class));

        if($this->storeData($payload , $user )){
            $this->logData("Product Create Job Successfull.");
        }else{
            $this->logData("Product Create Job Failed");
        }
    }
}
