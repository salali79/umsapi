<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterRegisterFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester_register_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_year_semester_id')->unsigned()->nullable();
            $table->integer('value')->nullable();
            $table->string('coin')->nullable();

            $table->foreign('study_year_semester_id')->references('id')
                ->on('study_year_semesters')->onDelete('set null');

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
        Schema::dropIfExists('semester_register_fees');
    }
}
