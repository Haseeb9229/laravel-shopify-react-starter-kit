<?php

namespace App\Repositories\OrderShippingAddress;

interface OrderShippingAddressRepositoryInterface
{
    public function getById(int $id);

    public function getByOrderId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);


}
