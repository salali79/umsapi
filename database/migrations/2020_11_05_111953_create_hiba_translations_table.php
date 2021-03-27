<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHibaTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hiba_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hiba_id')->unsigned();
            $table->string('name');
            $table->string('locale');

            $table->unique(['hiba_id', 'locale']);
            $table->foreign('hiba_id')->references('id')->on('hibas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('hiba_translations');

    }


}
