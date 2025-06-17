<?php

namespace App\Repositories\Order;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function getById(int $id);

    public function getByShopifyId(int $id);

    public function getByUserId(int $id);

    public function getByCustomerId(int $id);

    public function updateOrCreate(array $data);

    public function delete(int $id);

    public function SearchFilter($filters = []);
}
