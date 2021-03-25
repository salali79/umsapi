<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_stores', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->BigIncrements('id');
            $table->string('title')->unique();
            $table->unsignedBigInteger('store_type_id');
            $table->foreign('store_type_id')->references('id')->on('shopping_store_types')->onupdate('cascade')->ondelete('set null');
            $table->string('image');
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
        Schema::dropIfExists('shopping_stores');
    }
}
