<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            // ORDER DETAILS
            $table->integer('quantity')->default(0);
            $table->decimal('shipping',11,2)->default(0);
            $table->decimal('total',11,2)->default(0);

            // PERSONAL INFORMATION
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('address');
            $table->integer('zip_code');
            $table->string('state');
            $table->string('city');

            // PAYMENT INFORMATION
            $table->string('card_number');
            $table->string('cvv');
            $table->string('exp_month');
            $table->string('exp_year');

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
        Schema::dropIfExists('orders');
    }
}
