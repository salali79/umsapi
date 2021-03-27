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
            $table->engine='InnoDB';
            $table->BigIncrements('id');
            $table->unsignedBigInteger('order_id')->unsigned()->nullable()->default(null);
            $table->foreign('order_id')->references('id')->on('shopping_orders')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('product_id')->unsigned()->nullable()->default(null);
            $table->foreign('product_id')->references('id')->on('shopping_products')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('wallet_id')->unsigned()->nullable()->default(null);
            $table->foreign('wallet_id')->references('id')->on('shopping_wallets')->onupdate('cascade')->ondelete('set null');
            $table->integer('quantity')->default(1);
            $table->integer('item_order')->nullable();
            $table->integer('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
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
