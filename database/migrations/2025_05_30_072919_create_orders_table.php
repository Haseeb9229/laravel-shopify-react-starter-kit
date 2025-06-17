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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_customer_id')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('email')->nullable();
            $table->string('financial_status')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('name')->nullable();
            $table->text('note')->nullable();
            $table->string('phone')->nullable();
            $table->double('subtotal_price')->nullable();
            $table->string('tags')->nullable();
            $table->double('total_discounts')->nullable();
            $table->double('total_line_items_price')->nullable();
            $table->double('total_outstanding')->nullable();
            $table->double('total_price')->nullable();
            $table->double('total_shipping_price')->nullable();
            $table->double('total_tax')->nullable();
            $table->double('total_tip_received')->nullable();
            $table->double('total_weight')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
