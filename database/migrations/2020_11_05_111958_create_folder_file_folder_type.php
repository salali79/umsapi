<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderFileFolderType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_file_folder_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('folder_type_id')->unsigned();
            $table->bigInteger('folder_file_id')->unsigned(); 
            $table->bigInteger('study_year_id')->unsigned();
            $table->foreign('study_year_id')->references('id')->on('study_years');
            $table->foreign('folder_type_id')->references('id')->on('folder_types');
            $table->foreign('folder_file_id')->references('id')->on('folder_files');
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
        Schema::dropIfExists('folder_file_folder_type');
    }
}
