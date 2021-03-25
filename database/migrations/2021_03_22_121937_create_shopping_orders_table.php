<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_orders', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->BigIncrements('id');
            $table->float('total_price')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')->references('id')->on('shopping_wallets')->onupdate('cascade')->ondelete('set null');
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
        Schema::dropIfExists('shopping_orders');
    }
}
