<?php

namespace App\Repositories\ProductMedia;

interface ProductMediaRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function getByProductId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);


}
