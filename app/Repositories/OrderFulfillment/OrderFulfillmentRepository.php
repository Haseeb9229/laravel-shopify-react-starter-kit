<?php
namespace App\Repositories\OrderFulfillment;

use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\OrderFulfillment;
use App\Http\Resources\OrderFulfillmentResource;



class OrderFulfillmentRepository implements OrderFulfillmentRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(OrderFulfillment $OrderFulfillment)
    {
        $this->model = $OrderFulfillment;
    }
    public function getById(int $id)
    {
        $fulfillment = $this->model->find($id);
        return $fulfillment;
    }
    public function getByShopifyId(int $id)
    {
        $fulfillment = $this->model->where('shopify_order_fulfillment_id', $id)->first();
        return $fulfillment;
    }
    public function getByOrderId(int $id)
    {
        $fulfillments = $this->model->where('order_id', $id)->get();
        return $fulfillments;
    }
    public function updateOrCreate(array $data)
    {
        $fulfillment = $this->model->updateOrCreate([
            'shopify_order_fulfillment_id' => $data['shopify_order_fulfillment_id'],
            'order_id' => $data['order_id'],
        ], $data);
        return $fulfillment;

    }
    public function delete(int $id)
    {
        $fulfillment = $this->getById($id);
        $fulfillment->delete();
    }
}

