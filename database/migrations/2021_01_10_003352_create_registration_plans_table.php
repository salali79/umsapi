<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_year_semester_id')->unsigned()->nullable();
            $table->bigInteger('study_plan_id')->unsigned()->nullable();

            $table->bigInteger('faculty_id')->unsigned()->nullable();
            $table->bigInteger('department_id')->unsigned()->nullable();

            $table->foreign('study_year_semester_id')->references('id')
                ->on('study_year_semesters')->onDelete('set null');
            $table->foreign('study_plan_id')->references('id')
                ->on('study_plans')->onDelete('set null');


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
        Schema::dropIfExists('registration_plans');
    }
}
