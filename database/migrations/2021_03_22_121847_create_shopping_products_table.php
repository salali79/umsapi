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
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->references('id')->on('shopping_sections')->onupdate('cascade')->ondelete('set null');
            $table->string('name')->unique();
            $table->string('barcode');
            $table->text('description');
            $table->float('price');
            $table->string('image');
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
        Schema::dropIfExists('shopping_products');
    }
}
