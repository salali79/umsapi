<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('shopping_categories')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('shopping_stores')->onupdate('cascade')->ondelete('set null');
            $table->string('name')->unique();
            $table->string('barcode');
            $table->string('p_color');
            $table->text('description');
            $table->float('price');
            $table->string('image');
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
        Schema::dropIfExists('shopping_products');
    }
}
