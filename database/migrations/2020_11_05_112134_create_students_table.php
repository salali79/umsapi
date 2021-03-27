<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image')->nullable();
            $table->date('birthday')->nullable();
            $table->bigInteger('faculty_id')->unsigned();
            $table->bigInteger('register_way_id')->unsigned()->nullable();
            $table->foreign('register_way_id')->references('id')->on('register_ways');
            $table->bigInteger('folder_type_id')->unsigned()->nullable();
            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->date('registration_date')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('national_number')->nullable();
            $table->string('academic_number')->nullable();
            $table->bigInteger('study_year_id')->unsigned()->nullable();
            $table->foreign('study_year_id')->references('id')->on('study_years');
            $table->foreign('faculty_id')->references('id')->on('faculties');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('folder_type_id')->references('id')->on('folder_types');

            //$table->string('pincode')->nullable();
            //$table->text('cart_num')->nullable();

            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->integer('password_status')->default(0);
            $table->integer('item_order')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('students');
    }
}
