<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterWayTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_way_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('register_way_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('locale');
            $table->unique(['register_way_id', 'locale']);
  $table->foreign('register_way_id')->references('id')->on('register_ways')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_way_translations');
    }
}
