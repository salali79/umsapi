<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyPlanDetailTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plan_detail_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('study_plan_detail_id')->unsigned();

            $table->bigInteger('study_plan_id')->unsigned();

            $table->text('essential_books')->nullable();
            $table->text('secondary_books')->nullable();
            $table->text('study_way')->nullable();

            $table->string('locale');
            $table->unique(['study_plan_detail_id','locale'],'spd_id');
            $table->foreign('study_plan_detail_id')->references('id')->on('study_plan_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('study_plan_detail_translations');
    }
}
