<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('birthplace')->nullable();
            $table->char('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->text('specific_features')->nullable();
            $table->string('civil_record')->nullable();
            $table->string('amaneh')->nullable();
            $table->string('writing_hand')->nullable();
            $table->string('locale');
            $table->unique(['student_id', 'locale']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_translations');
    }
}
