<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fulfilment_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fulfillment_order_id')->nullable();
            $table->unsignedBigInteger('fulfilment_location_id')->nullable();
            $table->integer('order_id');
            $table->string('name')->nullable();
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
