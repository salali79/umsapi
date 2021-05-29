<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyPlanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plan_details', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('study_plan_id')->unsigned()->nullable();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->boolean('is_uc')->nullable();
            $table->boolean('is_fc')->nullable();
            $table->boolean('is_sc')->nullable();
            $table->boolean('mandatory')->nullable();
            $table->longText('prerequisite_ids')->nullable();
            $table->string('prerequisite_hours')->nullable();
            $table->string('credit_hours')->nullable();
            $table->string('theo_hours')->nullable();
            $table->string('practice_hours')->nullable();
            $table->bigInteger('level_id')->unsigned()->nullable();

            $table->foreign('study_plan_id')->references('id')->on('study_plans');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('level_id')->references('id')->on('study_plan_course_levels')->onDelete('cascade');

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
        Schema::dropIfExists('study_plan_details');
    }
}
