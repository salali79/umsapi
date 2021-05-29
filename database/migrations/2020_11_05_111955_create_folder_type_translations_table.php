<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('folder_type_id')->unsigned();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('locale');
            $table->unique(['folder_type_id', 'locale']);
            $table->foreign('folder_type_id')->references('id')->on('folder_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_type_translations');
    }
}
