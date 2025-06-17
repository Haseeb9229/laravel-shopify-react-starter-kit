<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function getByUserId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);


}
