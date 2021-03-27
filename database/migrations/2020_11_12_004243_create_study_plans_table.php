<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plans', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->date('version_date');
            $table->bigInteger('study_year_id')->unsigned()->nullable();
            $table->foreign('study_year_id')->references('id')->on('study_years');
            $table->integer('uoc_min_count')->nullable();
            $table->integer('foc_min_count')->nullable();

            $table->bigInteger('faculty_id')->unsigned()->nullable();
            $table->bigInteger('department_id')->unsigned()->nullable();

            $table->foreign('faculty_id')->references('id')->on('faculties');
            $table->foreign('department_id')->references('id')->on('departments');

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
        Schema::dropIfExists('study_plans');
    }
}
