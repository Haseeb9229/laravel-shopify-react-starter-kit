<?php
namespace App\Repositories\OrderShippingAddress;

use App\Http\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\Orders\OrderShippingAddress;
use App\Http\Resources\OrderShippingAddressResource;
use App\Repositories\OrderShippingAddress\OrderShippingAddressRepositoryInterface;


class OrderShippingAddressRepository implements OrderShippingAddressRepositoryInterface
{
    use ResponseTrait;
    protected $model;

    public function __construct(OrderShippingAddress $orderShippingAddress)
    {
        $this->model = $orderShippingAddress;
    }
    public function getById(int $id)
    {
        $shippingAddress = $this->model->find($id);
        return $shippingAddress;
    }
    public function getByOrderId(int $id)
    {
        $shippingAddress = $this->model->where('order_id', $id)->first();
        return $shippingAddress;
    }
    public function updateOrCreate(array $data)
    {
        $shippingAddress = $this->model->updateOrCreate([
            'order_id' => $data['order_id'],
        ], $data);
        return $shippingAddress;
    }
    public function delete(int $id)
    {
        $shippingAddress = $this->getById($id);
        $shippingAddress->delete();
    }
}

