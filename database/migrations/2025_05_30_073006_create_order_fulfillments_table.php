<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_order_fulfillment_id')->nullable();
            $table->unsignedBigInteger('shopify_order_fulfillment_location_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('name')->nullable();
            $table->string('service')->nullable();
            $table->string('shipment_status')->nullable();
            $table->string('status')->nullable();
            $table->string('tracking_company')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fulfilment_orders');
    }
};
