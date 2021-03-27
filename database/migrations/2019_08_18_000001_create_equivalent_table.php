<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquivalentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equivalents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hundred_equivalent_min')->nullable();
            $table->string('hundred_equivalent_max')->nullable();
            $table->string('char_equivalent')->nullable();
            $table->string('point_equivalent')->nullable();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('equivalents');
    }
}
