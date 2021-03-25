<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_product_attributes', function (Blueprint $table) {
            $table->engine='InnoDB';
            $table->BigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('shopping_products')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('shopping_departments')->onupdate('cascade')->ondelete('set null');
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references('id')->on('shopping_stores')->onupdate('cascade')->ondelete('set null');
            $table->integer('stock')->default(1);
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
        Schema::dropIfExists('shopping_product_attributes');
    }
}
