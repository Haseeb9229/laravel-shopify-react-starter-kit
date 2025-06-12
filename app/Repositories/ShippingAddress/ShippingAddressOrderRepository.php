<?php
namespace App\Repositories\ShippingAddress;

use App\Http\Traits\ResponseTrait;
use App\Models\Orders\ShippingAddressOrder;
use App\Repositories\ShippingAddress\ShippingAddressOrderRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ShippingAddressOrderResource;


class ShippingAddressOrderRepository implements ShippingAddressOrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;

    public function __construct(ShippingAddressOrder $shippingAddressOrder)
    {
        $this->model = $shippingAddressOrder;
    }
    public function getorderById(int $id)
    {
        $getOrderbyId = $this->model->where("order_id", $id)->first();
        return $getOrderbyId;
    }
    public function getShopifyOrderById(int $id)
    {
        $getShippingAddress = $this->model->where('order_id', $id)->first();
        return $getShippingAddress;
        // return new ShippingAddressOrderResource($getShippingAddress);
    }
    public function create(array $data)
    {
        if (!empty($data)) {
            $shippingCreated = $this->model->create($data);
            return new ShippingAddressOrderResource($shippingCreated);
        }
    }
    public function update(int $id, array $data, $shopify = false)
    {
        if ($shopify) {
            $shippingAddress = $this->getShopifyOrderById($id);
            if ($shippingAddress) {
                $shippingAddress->update($data);
                $this->sendResponse([true, "Shipping Address Update Successfully", $this->logData($data)], 200);
                return new ShippingAddressOrderResource($shippingAddress);
            } else {
                $this->sendResponse([false, "Shipping Address Update not found", $data], 404);
            }
        }
    }
    public function delete(int $id)
    {
        $Shippingorder = $this->getorderById($id);
        if ($Shippingorder) {
            return $Shippingorder->delete();
        } else {
            Log::error("Shipping Address in Repository Not Found for Deletion");
        }
    }
}

