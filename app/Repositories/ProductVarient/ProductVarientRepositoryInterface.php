<?php

namespace App\Repositories\ProductVarient;

interface ProductVarientRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function getByProductId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);


}
