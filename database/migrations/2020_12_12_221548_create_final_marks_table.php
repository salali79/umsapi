<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_year_id')->unsigned()->nullable();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->bigInteger('student_id')->unsigned()->nullable();
            $table->bigInteger('faculty_id')->unsigned()->nullable();//
            $table->bigInteger('department_id')->unsigned()->nullable();//



            $table->string('points')->nullable();
            $table->string('degree')->nullable();



            $table->foreign('study_year_id')->references('id')
                ->on('study_years')->onDelete('set null');
            $table->foreign('semester_id')->references('id')
                ->on('semesters')->onDelete('set null');
            $table->foreign('course_id')->references('id')
                ->on('courses')->onDelete('set null');
            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('set null');
            $table->foreign('faculty_id')->references('id')
                ->on('faculties')->onDelete('set null');
            $table->foreign('department_id')->references('id')
                ->on('departments')->onDelete('set null');



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
        Schema::dropIfExists('final_marks');
    }
}
