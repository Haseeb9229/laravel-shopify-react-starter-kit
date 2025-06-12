<?php

namespace App\Providers;

use App\Repositories\CustomerOrder\CustomerOrderRepository;
use App\Repositories\CustomerOrder\CustomerOrderRepositoryInterface;
use App\Repositories\FulfillmentOrder\FulfillmentOrderRepository;
use App\Repositories\FulfillmentOrder\FulfillmentOrderRepositoryInterface;
use App\Repositories\LineItemOrder\LineItemOrderRepository;
use App\Repositories\LineItemOrder\LineItemOrderRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\ShippingAddress\ShippingAddressOrderRepository;
use App\Repositories\ShippingAddress\ShippingAddressOrderRepositoryInterface;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\ProductVarient\ProductVarientRepositoryInterface;
use App\Repositories\ProductVarient\ProductVarientRepository;
use App\Repositories\ProductMedia\ProductMediaRepositoryInterface;
use App\Repositories\ProductMedia\ProductMediaRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
                ProductRepositoryInterface::class,
                ProductRepository::class
            );
        $this->app->bind(
                ProductVarientRepositoryInterface::class,
                ProductVarientRepository::class
            );
        $this->app->bind(
                ProductMediaRepositoryInterface::class,
                ProductMediaRepository::class
            );
        $this->app->bind(
                OrderRepositoryInterface::class,
                OrderRepository::class
            );
     
        $this->app->bind(
            CustomerOrderRepositoryInterface::class,
                CustomerOrderRepository::class
            );
        $this->app->bind(
            FulfillmentOrderRepositoryInterface::class,
                FulfillmentOrderRepository::class
            );
        $this->app->bind(
            ShippingAddressOrderRepositoryInterface::class,
                ShippingAddressOrderRepository::class
            );
        $this->app->bind(
            LineItemOrderRepositoryInterface::class,
                LineItemOrderRepository::class
            );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \URL::forceScheme('https');
        Vite::prefetch(concurrency: 3);
    }
}
