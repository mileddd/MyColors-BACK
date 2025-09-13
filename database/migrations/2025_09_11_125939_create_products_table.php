<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('product_name',255);
            $table->double('product_price');
            $table->bigInteger('product_stock')->default(0);
        });

        DB::table('products')->insert([
            [
                'product_name'     => 'Product 1',
                'product_price'    => '30',
                'product_stock'    =>  40,
            ],
            [
                'product_name'     => 'Product 2',
                'product_price'    => '25.5',
                'product_stock'    => 0,
            ],
            [
                'product_name'     => 'Product 3',
                'product_price'    => '50',
                'product_stock'    => 5,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
