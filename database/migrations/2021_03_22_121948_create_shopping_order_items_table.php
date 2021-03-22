<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unsigned()->nullable()->default(null);
            $table->foreign('order_id')->references('id')->on('shopping_orders')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('product_id')->unsigned()->nullable()->default(null);
            $table->foreign('product_id')->references('id')->on('shopping_products')->onupdate('cascade')->ondelete('set null');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('shopping_order_items');
    }
}
