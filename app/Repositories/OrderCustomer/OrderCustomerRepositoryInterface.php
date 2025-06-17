<?php

namespace App\Repositories\OrderCustomer;

interface OrderCustomerRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);
}
