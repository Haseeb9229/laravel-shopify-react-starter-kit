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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_order_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('currency')->nullable();
            $table->string('email')->nullable();
            $table->string('financial_status')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('tags')->nullable();
            $table->unsignedBigInteger('subtotal_price')->nullable();
            $table->unsignedBigInteger('total_discounts')->nullable();
            $table->unsignedBigInteger('total_line_items_price')->nullable();
            $table->unsignedBigInteger('total_outstanding')->nullable();
            $table->unsignedBigInteger('total_price')->nullable();
            $table->unsignedBigInteger('total_tax')->nullable();
            $table->unsignedBigInteger('total_weight')->nullable();
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
