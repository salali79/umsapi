<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyPlanCourseLevelTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plan_course_level_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->bigInteger('study_plan_course_level_id')->unsigned();

            $table->string('locale');
            $table->unique(['study_plan_course_level_id','locale'],'spcl_local');
            $table->foreign('study_plan_course_level_id','spcl_id_foreign')->references('id')->on('study_plan_course_levels')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('study_plan_course_level_translations');
    }
}
