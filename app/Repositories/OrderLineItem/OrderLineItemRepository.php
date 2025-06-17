<?php
namespace App\Repositories\OrderLineItem;

use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\OrderLineItem;
use App\Http\Resources\OrderLineItemResource;
use App\Repositories\OrderLineItem\OrderLineItemRepositoryInterface;


class OrderLineItemRepository implements OrderLineItemRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(OrderLineItem $orderLineItem)
    {
        $this->model = $orderLineItem;
    }
    public function getById(int $id)
    {
        $lineItem = $this->model->find($id);
        return $lineItem;
    }
    public function getByShopifyId(int $id)
    {
        $lineItem = $this->model->where('shopify_order_lineitem_id', $id)->first();
        return $lineItem;
    }
    public function getByOrderId(int $id)
    {
        $lineItems = $this->model->where('order_id', $id)->get();
        return $lineItems;
    }
    public function updateOrCreate(array $data)
    {
        $lineItem = $this->model->updateOrCreate([
            'shopify_order_lineitem_id' => $data['shopify_order_lineitem_id'],
            'order_id' => $data['order_id'],
        ], $data);
        return $lineItem;
    }
    public function delete(int $id)
    {
        $lineItem = $this->getById($id);
        $lineItem->delete();
    }
}

