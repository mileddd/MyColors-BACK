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
        Schema::create('user_cart', function (Blueprint $table) {
            $table->bigIncrements('usercart_id');
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->bigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_carts');
    }
};
