<?php

namespace App\Http\Traits;

use Log;
use App\Models\User;
use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Repositories\Order\OrderRepositoryInterface;

trait ShopifyOrderTrait
{
    use ResponseTrait;
    protected $order;
    public function getOrderRepository(OrderRepositoryInterface $order)
    {
        $this->order = $order;
    }
    public function getOrdersFromShopify(User $user)
    {
        try {
            $orderCount = $this->getOrdersCountFromShopify($user);
            $cursor = 'null';
            $loop = ceil($orderCount / 250);
            $hasErrors = false;
            for ($i = 1; $i <= $loop; $i++) {
                [$orders, $nextCursor] = $this->shopifyGraphqlOrderQuery($user, $cursor);
                if ($orders && $nextCursor) {
                    $cursor = '"' . $nextCursor . '"';
                    foreach ($orders as $order) {
                        $order = $this->transformShopifyOrderData($order);
                        Log::info("Order Data: " . json_encode($order, JSON_PRETTY_PRINT));
                        if (!$this->storeData($this->arrayToObject($order), $user)) {
                            $hasErrors = true;
                        }
                    }
                }
            }
            if ($hasErrors) {
                throw new \Exception("Some Orders could not be stored.");
            }
        } catch (\Exception $e) {
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        return true;
    }
    public function getOrdersCountFromShopify($user)
    {
        $query = <<<QUERY
            query{
                ordersCount(limit: 2000){
                    count
                    precision
                }
            }
        QUERY;
        $result = $this->arrayToObject($user->api()->graph($query));
        Log::info("Orders Count Query Result: " . json_encode($result, JSON_PRETTY_PRINT));
        if ($result->errors) {
            return 0;
        } else {
            return $result->body->data->ordersCount->count;
        }
    }
    public function shopifyGraphqlOrderQuery($user, $cursor)
    {
        $query = <<<QUERY
            query {
                orders(first: 250, after: $cursor) {
                    edges {
                        node {
                            id
                            email
                            displayFinancialStatus
                            displayFulfillmentStatus
                            name
                            note
                            phone
                            subtotalPriceSet{
                                shopMoney {
                                    amount
                                }
                            }
                            tags
                            totalDiscountsSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalOutstandingSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalPriceSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalShippingPriceSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalTaxSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalTipReceivedSet{
                                shopMoney {
                                    amount
                                }
                            }
                            totalWeight
                            customer {
                                id
                                email
                                firstName
                                lastName
                                phone
                            }
                            lineItems(first: 250) {
                                edges {
                                    node {
                                        id
                                        originalUnitPriceSet {
                                            shopMoney {
                                                amount
                                            }
                                        }
                                        quantity
                                        sku
                                        title
                                        totalDiscountSet {
                                            shopMoney {
                                                amount
                                            }
                                        }
                                        variant {
                                            id
                                        }
                                    }
                                }
                            }
                            shippingAddress {
                                firstName
                                lastName
                                address1
                                phone
                                city
                                zip
                                province
                                country
                                company
                                countryCodeV2
                                provinceCode
                            }
                            fulfillments(first: 250){
                                id
                                location{
                                    id
                                }
                                name
                                service{
                                    type
                                }
                                displayStatus
                                status
                                trackingInfo{
                                    company
                                    number
                                    url
                                }
                            }
                        }
                    }
                    pageInfo {
                        hasNextPage
                        endCursor
                    }
                }
            }
        QUERY;
        $result = $this->arrayToObject($user->api()->graph($query));
        if ($result->errors) {
            return [null, null];
        } else {
            $orders = $result->body->data->orders->edges;
            $cursor = $result->body->data->orders->pageInfo->endCursor;
            return [$orders, $cursor];
        }
    }
    public function storeData($order, User $user, $update = false)
    {
        DB::beginTransaction();
        try {
            $formatedData = $this->formateOrderData($order, $user);
            if ($update) {
                $order = $this->order->getByShopifyId($order->id);
                if (!$order) {
                    Log::info("Order May be deleted: " . json_encode($order, JSON_PRETTY_PRINT));
                    return true;
                }
            }
            $this->order->updateOrCreate($formatedData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to store Order: " . json_encode($order));
            Log::error("Exception: " . json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function formateOrderData($order, $user)
    {
        $formatedOrder = [
            "shopify_order_id" => $order->id,
            "user_id" => $user->id,
            "contact_email" => $order->contact_email,
            "email" => $order->email,
            "financial_status" => strtoupper($order->financial_status) ?? 'UNPAID',
            "fulfillment_status" => $order->fulfillment_status ? strtoupper($order->fulfillment_status) : 'UNFULFILLED',
            "name" => $order->name,
            "note" => $order->note,
            "phone" => $order->phone,
            "subtotal_price" => $order->subtotal_price,
            "tags" => $order->tags,
            "total_discounts" => $order->total_discounts,
            "total_line_items_price" => $order->total_line_items_price,
            "total_outstanding" => $order->total_outstanding,
            "total_price" => $order->total_price,
            "total_shipping_price" => $order->total_shipping_price_set->shop_money->amount ?? 0,
            "total_tax" => $order->total_tax,
            "total_tip_received" => $order->total_tip_received,
            "total_weight" => $order->total_weight,
            "customer" => $this->formateOrderCustomerData($order->customer),
            "line_items" => $this->formateOrderLineItemsData($order->line_items),
            "shipping_address" => $this->formateOrderShippingAddressData($order->shipping_address),
            "fulfillments" => $this->formateOrderFulfillmentsData($order->fulfillments)
        ];
        return $formatedOrder;
    }
    public function formateOrderCustomerData($customer)
    {
        if (!$customer) {
            return null;
        }
        $orderCustomer = [
            "shopify_customer_id" => $customer->id,
            "email" => $customer->email,
            "first_name" => $customer->first_name,
            "last_name" => $customer->last_name,
            "phone" => $customer->phone,
        ];
        return $orderCustomer;
    }
    public function formateOrderLineItemsData($lineItems)
    {
        $orderLineItems = [];
        foreach ($lineItems as $item) {
            $orderLineItems[] = [
                "shopify_order_lineitem_id" => $item->id,
                "price" => $item->price,
                "quantity" => $item->quantity,
                "sku" => $item->sku,
                "title" => $item->title,
                "total_discount" => $item->total_discount,
                "shopify_product_variant_id" => $item->variant_id,
            ];
        }
        return $orderLineItems;
    }
    public function formateOrderShippingAddressData($shippingAddress)
    {
        if (!$shippingAddress) {
            return null;
        }
        $orderShippingAddress = [
            "first_name" => $shippingAddress->first_name,
            "last_name" => $shippingAddress->last_name,
            "address1" => $shippingAddress->address1,
            "phone" => $shippingAddress->phone,
            "city" => $shippingAddress->city,
            "zip" => $shippingAddress->zip,
            "province" => $shippingAddress->province,
            "country" => $shippingAddress->country,
            "company" => $shippingAddress->company,
            "country_code" => $shippingAddress->country_code,
            "province_code" => $shippingAddress->province_code
        ];
        return $orderShippingAddress;
    }
    public function formateOrderFulfillmentsData($fulfillment)
    {
        $orderFulfillments = [];
        foreach ($fulfillment as $fulfill) {
            $orderFulfillments[] = [
                "shopify_order_fulfillment_id" => $fulfill->id,
                "shopify_order_fulfillment_location_id" => $fulfill->location_id,
                "name" => $fulfill->name,
                "service" => strtoupper($fulfill->service),
                "shipment_status" => strtoupper($fulfill->shipment_status),
                "status" => strtoupper($fulfill->status),
                "tracking_company" => $fulfill->tracking_company,
                "tracking_number" => $fulfill->tracking_number,
                "tracking_url" => $fulfill->tracking_url,
            ];
        }
        return $orderFulfillments;
    }
    public function deleteOrder($orderId)
    {
        DB::beginTransaction();
        try {
            $order = $this->order->getByShopifyId($orderId);
            $this->order->delete($order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return false;
        }
        DB::commit();
        return true;
    }
    public function transformShopifyOrderData($data): array
    {
        $node = $data->node;
        $orderLineItems = [];
        if (!empty($node->lineItems->edges)) {
            foreach ($node->lineItems->edges as $edge) {
                $lineItem = $edge->node;
                $orderLineItems[] = [
                    'id' => $this->extractId($lineItem->id),
                    'price' => $lineItem->originalUnitPriceSet->shopMoney->amount ?? null,
                    'quantity' => $lineItem->quantity ?? null,
                    'sku' => $lineItem->sku ?? null,
                    'title' => $lineItem->title ?? null,
                    'total_discount' => $lineItem->totalDiscountSet->shopMoney->amount ?? 0,
                    'variant_id' => $lineItem->variant?->id ? $this->extractId($lineItem->variant->id) : null,
                ];
            }
        }
        $fulfillments = [];
        if (!empty($node->fulfillments)) {
            foreach ($node->fulfillments as $fulfillment) {
                $fulfillments[] = [
                    "id" => $this->extractId($fulfillment->id),
                    "location_id" => $this->extractId($fulfillment->location->id ?? null),
                    "name" => $fulfillment->name,
                    "service" => $fulfillment->service->type,
                    "shipment_status" => $fulfillment->displayStatus,
                    "status" => $fulfillment->status,
                    "tracking_company" => $fulfillment->trackingInfo[0]->company,
                    "tracking_number" => $fulfillment->trackingInfo[0]->number,
                    "tracking_url" => $fulfillment->trackingInfo[0]->url,
                ];
            }
        }
        $customer = $node->customer;
        if (!empty($customer)) {
            $customer = [
                'id' => $this->extractId($customer->id),
                "email" => $customer->email ?? null,
                "first_name" => $customer->firstName ?? null,
                "last_name" => $customer->lastName ?? null,
                "phone" => $customer->phone ?? null,
            ];
        }
        $shippingAddress = $node->shippingAddress;
        if (!empty($shippingAddress)) {
            $shippingAddress = [
                "first_name" => $shippingAddress->firstName,
                "last_name" => $shippingAddress->lastName,
                "address1" => $shippingAddress->address1,
                "phone" => $shippingAddress->phone,
                "city" => $shippingAddress->city,
                "zip" => $shippingAddress->zip,
                "province" => $shippingAddress->province,
                "country" => $shippingAddress->country,
                "company" => $shippingAddress->company,
                "country_code" => $shippingAddress->countryCodeV2,
                "province_code" => $shippingAddress->provinceCode
            ];
        }
        $order = [
            'id' => $this->extractId($node->id),
            "contact_email" => $node->email,
            "email" => $node->email,
            "financial_status" => $node->displayFinancialStatus,
            "fulfillment_status" => $node->displayFulfillmentStatus,
            "name" => $node->name,
            "note" => $node->note,
            "phone" => $node->phone,
            "subtotal_price" => $node->subtotalPriceSet->shopMoney->amount ?? 0,
            "tags" => $this->arrayToString($node->tags),
            "total_discounts" => $node->totalDiscountsSet->shopMoney->amount ?? 0,
            "total_line_items_price" => 0,
            "total_outstanding" => $node->totalOutstandingSet->shopMoney->amount ?? 0,
            "total_price" => $node->totalPriceSet->shopMoney->amount ?? 0,
            "total_shipping_price" => $node->totalShippingPriceSet->shopMoney->amount ?? 0,
            "total_tax" => $node->totalTaxSet->shopMoney->amount ?? 0,
            "total_tip_received" => $node->totalTipReceivedSet->shopMoney->amount ?? 0,
            "total_weight" => $node->totalWeight,
            'line_items' => $orderLineItems,
            'customer' => $customer,
            'shipping_address' => $shippingAddress,
            'fulfillments' => $fulfillments,
        ];
        return $order;
    }
    public function arrayToObject($data)
    {
        return json_decode(json_encode($data));
    }
    public function arrayToString($data)
    {
        if (is_array($data)) {
            if (empty($data)) {
                return '';
            } else {
                return implode(',', $data);
            }
        }
        return $data;
    }
    public function extractId($id)
    {
        $arr = explode('/', $id);
        return end($arr);
    }
}
