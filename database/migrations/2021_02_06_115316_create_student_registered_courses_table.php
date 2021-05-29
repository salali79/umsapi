<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRegisteredCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_registered_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned()->nullable();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->bigInteger('registration_plan_id')->unsigned()->nullable();
            $table->bigInteger('registration_course_category_id')->unsigned()->nullable();
            $table->bigInteger('registration_course_group_id')->unsigned()->nullable();



            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('set null');

            $table->foreign('course_id')->references('id')
                ->on('courses')->onDelete('set null');

            $table->foreign('registration_plan_id')->references('id')
                ->on('registration_plans')->onDelete('set null');

            $table->foreign('registration_course_category_id','student_registration_course_category_id_foreign')->references('id')
                ->on('registration_course_categories')->onDelete('set null');

            $table->foreign('registration_course_group_id','student_registration_course_group_id_foreign')->references('id')
                ->on('registration_course_groups')->onDelete('set null');

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
        Schema::dropIfExists('student_registered_courses');
    }
}
