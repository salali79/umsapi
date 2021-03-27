<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSemesterTranscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_semester_transcripts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned()->nullable();
            $table->bigInteger('study_year_id')->unsigned()->nullable();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->string('s_completed_hours')->nullable();
            $table->string('a_completed_hours')->nullable();
            $table->string('s_registered_hours')->nullable();
            $table->string('a_registered_hours')->nullable();
            $table->string('gpa')->nullable();
            $table->string('agpa')->nullable();

            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('set null');
            $table->foreign('study_year_id')->references('id')
                ->on('study_years')->onDelete('set null');
            $table->foreign('semester_id')->references('id')
                ->on('semesters')->onDelete('set null');

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
        Schema::dropIfExists('student_semester_transcripts');
    }
}
