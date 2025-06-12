<?php

namespace App\Repositories\CustomerOrder;

interface CustomerOrderRepositoryInterface
{
    public function getOrderById(int $id);

    public function create(array $data);    

    public function update(int $id, array $data , $update = true);

    public function delete(int $id);


}