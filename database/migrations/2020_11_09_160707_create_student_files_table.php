<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_files', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('file_id')->unsigned();
            $table->string('image')->nullable();
            $table->date('date')->nullable();
            $table->integer('count')->nullable();
            $table->string('pdf')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('file_id')->references('id')->on('folder_files');
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
        Schema::dropIfExists('student_files');
    }
}
