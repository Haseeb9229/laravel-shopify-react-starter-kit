<?php
namespace App\Repositories\Order;

use App\Models\Orders\Order;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\OrderCustomer\OrderCustomerRepositoryInterface;
use App\Repositories\OrderLineItem\OrderLineItemRepositoryInterface;
use App\Repositories\OrderFulfillment\OrderFulfillmentRepositoryInterface;
use App\Repositories\OrderShippingAddress\OrderShippingAddressRepositoryInterface;


class OrderRepository implements OrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    protected $orderCustomer;
    protected $orderLineItems;
    protected $orderShippingAddress;
    protected $orderFulfillments;

    public function __construct(Order $order, OrderCustomerRepositoryInterface $orderCustomer, OrderLineItemRepositoryInterface $orderLineItems, OrderShippingAddressRepositoryInterface $orderShippingAddress, OrderFulfillmentRepositoryInterface $orderFulfillments)
    {
        $this->model = $order;
        $this->orderCustomer = $orderCustomer;
        $this->orderLineItems = $orderLineItems;
        $this->orderShippingAddress = $orderShippingAddress;
        $this->orderFulfillments = $orderFulfillments;
    }
    public function getById(int $id)
    {
        $order = $this->model->find($id);
        return $order;
    }
    public function getByShopifyId(int $id)
    {
        $order = $this->model->where('shopify_order_id', $id)->first();
        return $order;
    }
    public function getByUserId(int $id)
    {
        $orders = $this->model->where('user_id', $id)->get();
        return $orders;
    }
    public function getByCustomerId(int $id)
    {
        $orders = $this->model->where('order_customer_id', $id)->get();
        return $orders;
    }
    public function updateOrCreate(array $data)
    {
        $customer = $data["customer"];
        unset($data["customer"]);

        $shipping_address = $data["shipping_address"];
        unset($data["shipping_address"]);

        $lineitems = $data["line_items"];
        unset($data["line_items"]);

        $fulfillments = $data["fulfillments"];
        unset($data["fulfillments"]);

        $order = $this->model->updateOrCreate([
            'shopify_order_id' => $data['shopify_order_id'],
            'user_id' => $data['user_id']
        ], $data);

        if ($customer) {
            $customer = $this->orderCustomer->updateOrCreate($customer);
            $order->orderCustomer()->associate($customer);
            $order->save();
        }

        if ($shipping_address) {
            $shipping_address["order_id"] = $order->id;
            $this->orderShippingAddress->updateOrCreate($shipping_address);
        }

        foreach ($lineitems as $item) {
            $item["order_id"] = $order->id;
            $this->orderLineItems->updateOrCreate($item);
        }
        foreach ($fulfillments as $fulfillment) {
            $fulfillment["order_id"] = $order->id;
            $this->orderFulfillments->updateOrCreate($fulfillment);
        }

        return $order;
    }
    public function delete(int $id)
    {
        $order = $this->getById($id);
        $shippingAddress = $this->orderShippingAddress->getByOrderId($order->id);
        $lineItems = $this->orderLineItems->getByOrderId($order->id);
        $fulfillments = $this->orderFulfillments->getByOrderId($order->id);
        $this->orderShippingAddress->delete($shippingAddress->id);
        foreach ($lineItems as $lineItem) {
            $this->orderLineItems->delete($lineItem->id);
        }
        foreach ($fulfillments as $fulfillment) {
            $this->orderFulfillments->delete($fulfillment->id);
        }
        $order->delete();
    }
    public function SearchFilter($filters = [])
    {
        $user = auth()->user();
        $orders = $user->orders()->with($filters['relation'])
            ->when($filters['financial_status'], function ($q) use ($filters) {
                $q->where('financial_status', $filters['financial_status']);
            })->when($filters['fulfillment_status'], function ($q) use ($filters) {
                $q->where('fulfillment_status', $filters['fulfillment_status']);
            })
            ->when($filters['query'], function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['query']}%")
                    ->orWhere('financial_status', 'LIKE', "%{$filters['query']}%")
                    ->orWhere("fulfillment_status", "LIKE", "%{$filters['query']}%")
                    ->orWhereHas('orderCustomer', function ($q) use ($filters) {
                        $q->where('first_name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('last_name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('email', 'LIKE', "%{$filters['query']}%");
                    });
            })
            ->get();

        return response()->json($orders);
    }
}

