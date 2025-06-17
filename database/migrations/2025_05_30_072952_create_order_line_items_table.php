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
        Schema::create('order_line_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_order_lineitem_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('sku')->nullable();
            $table->string('title')->nullable();
            $table->double('total_discount')->nullable();
            $table->unsignedBigInteger('shopify_product_variant_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_item_orders');
    }
};
