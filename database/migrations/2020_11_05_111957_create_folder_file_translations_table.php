<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderFileTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_file_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('folder_file_id')->unsigned();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('locale');
            $table->unique(['folder_file_id', 'locale']);
            $table->foreign('folder_file_id')->references('id')->on('folder_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_file_translations');
    }
}
