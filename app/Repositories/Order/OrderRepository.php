<?php
namespace App\Repositories\Order;

use App\Http\Resources\OrderResource;
use App\Http\Traits\ResponseTrait;
use App\Models\Orders\Order;
use App\Repositories\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\FulfillmentOrder\FulfillmentOrderRepositoryInterface;
use App\Repositories\LineItemOrder\LineItemOrderRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\ShippingAddress\ShippingAddressOrderRepositoryInterface;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class OrderRepository implements OrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    protected $CustomerOrder;
    protected $LineItemOrder;
    protected $ShippingAddressOrder;
    protected $FulfillmentOrder;

    public function __construct(Order $order, CustomerOrderRepositoryInterface $CustomerOrder, LineItemOrderRepositoryInterface $LineItemOrder, ShippingAddressOrderRepositoryInterface $ShippingAddressOrder, FulfillmentOrderRepositoryInterface $FulfillmentOrder)
    {
        $this->model = $order;
        $this->CustomerOrder = $CustomerOrder;
        $this->LineItemOrder = $LineItemOrder;
        $this->ShippingAddressOrder = $ShippingAddressOrder;
        $this->FulfillmentOrder = $FulfillmentOrder;
    }
    public function getOrderById(int $id)
    {
        $getOrderID = $this->model->find($id);
        return new OrderResource($getOrderID);
    }
    public function getShopifyOrderById(int $id)
    {
        $getOrder = $this->model->where('shopify_order_id', $id)->first();
        return $getOrder;
        // return new OrderResource($getOrder);
    }
    public function create(array $data)
    {
        if ($data) {

            $customer = $data["customer"];
            unset($data["customer"]);

            $shipping_address = $data["shipping_address"];
            unset($data["shipping_address"]);

            $lineitems = $data["line_items"];
            unset($data["line_items"]);

            $fulfillments = $data["fulfillments"];
            unset($data["fulfillments"]);

            $order = $this->model->create($data);

            $customer["order_id"] = $order->id;
            $this->CustomerOrder->create($customer);

            $shipping_address["order_id"] = $order->id;
            $this->ShippingAddressOrder->create($shipping_address);

            foreach ($lineitems as $item) {
                $item["order_id"] = $order->id;
                $this->LineItemOrder->create($item);
            }

            foreach ($fulfillments as $fulfillment) {
                $fulfillment["order_id"] = $order->id;
                $this->FulfillmentOrder->create($fulfillment);
            }
            return new OrderResource($order);
        } else {
            return $this->sendResponse([false, "Order Data not found", $data], 404);
        }

    }
    public function update(int $id, array $data, $shopify = false)
    {
        if ($shopify) {
            $order = $this->getShopifyOrderById($id);
            if ($order) {
                $customer = $data["customer"];
                unset($data["customer"]);

                $shipping_address = $data["shipping_address"];
                unset($data["shipping_address"]);

                $line_items = $data["line_items"];
                unset($data["line_items"]);

                $fulfillments = $data["fulfillments"];
                unset($data["fulfillments"]);

                $order->update($data);

                $customer["order_id"] = $order->id;
                $this->CustomerOrder->update($customer["shopify_customer_id"], $customer, true);

                $shipping_address["order_id"] = $order->id;
                $this->ShippingAddressOrder->update($shipping_address["order_id"], $shipping_address, true);

                foreach ($line_items as $item) {
                    $item["order_id"] = $order->id;
                    $this->LineItemOrder->update($item["shopify_lineitem_id"], $item, true);
                }
                foreach ($fulfillments as $fulfillment) {
                    $fulfillment["order_id"] = $order->id;
                    $this->FulfillmentOrder->update($fulfillment["fulfillment_order_id"], $fulfillment, true);
                }
                return new OrderResource($order);
            } else {
                Log::info("Order Not Found.! Try Again.");
            }
        } else {
            $Orders = $this->getOrderById($id);
            foreach ($Orders as $order) {
                $order->update($data);
                return new OrderResource($order);
            }
        }
    }
    public function delete(int $id)
    {
        $order = $this->getShopifyOrderById($id);
        if ($order) {
            $this->CustomerOrder->delete($order->id);
            $this->ShippingAddressOrder->delete($order->id);
            $this->LineItemOrder->delete($order->id);
            $this->FulfillmentOrder->delete($order->id);
            return $order->delete();
        } else {
            Log::error("Order in Repository Not Found for Deletion");
        }
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
                    ->orWhereHas('CustomerOrder', function ($q) use ($filters) {
                        $q->where('first_name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('last_name', 'LIKE', "%{$filters['query']}%")
                            ->orWhere('email', 'LIKE', "%{$filters['query']}%");
                    });
            })
            ->get();

        return response()->json($orders);
    }
    // public function SyncOrder()
    // {
       
    // }
}

