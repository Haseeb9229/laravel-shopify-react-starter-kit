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
        Schema::create('line_item_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_lineitem_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('quantity')->nullable();
            $table->string('title')->nullable();
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
