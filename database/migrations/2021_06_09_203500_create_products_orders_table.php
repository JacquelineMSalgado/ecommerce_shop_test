<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('actual_price',11,2)->default(0);
            $table->integer('quantity')->default(0);

            $table->integer('product_id')->unsigned();
            $table->integer('order_id')->unsigned();

            // REFERENCES
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('order_id')->references('id')->on('orders');

            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_orders');
    }
}
