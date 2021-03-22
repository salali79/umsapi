<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_purchases', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->BigIncrements('id');
            $table->float('price');
            $table->date('date');
            $table->morphs('purchaseable');
            $table->unsignedBigInteger('order_id')->unsigned()->nullable()->default(null);
            $table->foreign('order_id')->references('id')->on('shopping_orders')->onupdate('cascade')->ondelete('set null');
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
        Schema::dropIfExists('shopping_purchases');
    }
}
