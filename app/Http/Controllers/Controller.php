<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseTrait;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;

abstract class Controller
{
    use ResponseTrait;
 
}
