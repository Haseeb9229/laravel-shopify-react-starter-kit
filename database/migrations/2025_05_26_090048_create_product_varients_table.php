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
        Schema::create('product_varients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_product_varient_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->bigInteger('price')->nullable();
            $table->string('sku')->nullable();
            $table->string('title')->nullable();
            $table->double('compare_at_price')->nullable();
            $table->bigInteger('inventory_quantity')->nullable();
            $table->unsignedBigInteger('shopify_inventory_item_id')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_varients');
    }
};
