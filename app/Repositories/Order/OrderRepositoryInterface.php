<?php

namespace App\Repositories\Order;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    public function getOrderById(int $id);

    public function create(array $data);    

    public function update(int $id, array $data , $update = true);

    public function delete(int $id);

    public function SearchFilter($filters = []);
}