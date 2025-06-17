<?php

namespace App\Repositories\OrderLineItem;

interface OrderLineItemRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function getByOrderId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);


}
