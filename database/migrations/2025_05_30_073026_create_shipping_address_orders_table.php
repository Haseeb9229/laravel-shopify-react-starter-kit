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
        Schema::create('shipping_address_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address1')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('zip')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('company')->nullable();
            $table->string('country_code')->nullable();
            $table->string('province_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_address_orders');
    }
};
