<?php

namespace App\Http\Traits;
use App\Models\User;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Http\Traits\ResponseTrait;
use Log;
use Illuminate\Support\Facades\DB;

trait ShopifyOrderTrait
{
    use ResponseTrait;
    protected $Order;
    public function getOrderRepository(OrderRepositoryInterface $Order){   
        $this->Order = $Order;  
    }
    public function FormateOrderData($order , $user){
        $orders = [
            "user_id" => $user->id,
            "shopify_order_id" => $order->id, 
            "contact_email" => $order->contact_email,
            "currency" => $order->currency,
            "email" => $order->email, 
            "financial_status" => $order->financial_status? : 'unpaid',
            "fulfillment_status" => $order->fulfillment_status? : 'unfulfilled',
            "name" => $order->name,
            "phone" => $order->phone,
            "subtotal_price" => $order->subtotal_price,
            "tags" => $order->tags,
            "total_discounts" => $order->total_discounts,
            "total_line_items_price" => $order->total_line_items_price,
            "total_outstanding" => $order->total_outstanding,
            "total_price" => $order->total_price,
            "total_tax" => $order->total_tax,
            "total_weight" => $order->total_weight,
            "customer" => $this->FormateOrderCustomerData($order->customer),
            "line_items" => $this->FormateOrderLineItemsData($order->line_items),
            "shipping_address" => $this->FormateOrderShippingAddressData($order->shipping_address),
            "fulfillments" => $this->FormateOrderFulfillmentsData($order->fulfillments)
            ];
          return $orders;
    }
    public function FormateOrderCustomerData($customer){
       $Customer = [
            "shopify_customer_id" => $customer->id,
            "order_id" =>  null,
            "email" => $customer->email,
            "first_name" => $customer->first_name,
            "last_name" => $customer->last_name,
            "phone" => $customer->phone,
       ]; 
       return $Customer;
    }
    public function FormateOrderLineItemsData($lineItem){
        $lineItems = [];
        foreach($lineItem as $item){            
            $lineItems[] = [
                "shopify_lineitem_id" => $item->id,
                "order_id" => null,
                "variant_id" => $item->variant_id,
                "name"=> $item->name,
                "price"=> $item->price,
                "quantity" => $item->quantity,
                "title" => $item->title,
                ];
        }
        return $lineItems;
    }
    public function FormateOrderFulfillmentsData($fulfillment){
        $fulfillments = [];
        foreach($fulfillment as $fulfill){            
            $fulfillments[] = [
                "fulfillment_order_id" => $fulfill->id,
                "fulfilment_location_id" => $fulfill->location_id,
                "order_id" => null,
                "name" => $fulfill->name,
                "shipment_status"=> $fulfill->shipment_status,
                "status"=> $fulfill->status,
                "tracking_company" => $fulfill->tracking_company,
                "tracking_number" => $fulfill->tracking_number,
                "tracking_url" => $fulfill->tracking_url,
                ];
        }
        return $fulfillments;
    } 
    public function FormateOrderShippingAddressData($shippingAddress){
        $shippingAddress = [
            "order_id" => null,
            "first_name" => $shippingAddress->first_name,
            "last_name" => $shippingAddress->last_name,
            "address1" => $shippingAddress->address1,
            "phone" => $shippingAddress->phone,
            "city"=> $shippingAddress->city,
            "zip" => $shippingAddress->zip,
            "province" => $shippingAddress->province,
            "country" => $shippingAddress->country,
            "company" => $shippingAddress->company,
            "country_code" => $shippingAddress->country_code,
            "province_code"=> $shippingAddress->province_code
        ];
        return $shippingAddress;
    }
    public function StoreDataToDatabase($order , User $user , $shopify = false){
        DB::beginTransaction();
        try {
            Log::info("Order into Database!!....");
            $this->storeData($order , $user , $shopify);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function storeData($order , User $user , $shopify = false){
        $formattedData = $this->FormateOrderData($order , $user);
        if ($shopify) {
            $this->Order->update($formattedData['shopify_order_id'], $formattedData, true);
            Log::info("Order Updated Successfully from traits!" , ["shopify_order_id" => $formattedData]);
        } else {
            $this->Order->create($formattedData);
            Log::info("Order Created Successfully from traits!!");
        }
    }
    public function DeleteOrder($prouductId){   
        DB::beginTransaction();
        try {
            $this->Order->delete($prouductId->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
}
