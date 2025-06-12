<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function getProductById(int $id);

    public function create(array $data);    

    public function update(int $id, array $data , $update = true);

    public function delete(int $id);


}