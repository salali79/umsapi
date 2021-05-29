<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentMedicalTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_medical_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_medical_id')->unsigned();
            $table->text('diagnostic')->nullable();
            $table->string('doctor_name')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->string('locale');
            $table->unique(['student_medical_id','locale']);
            $table->foreign('student_medical_id')->references('id')->on('student_medicals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_medical_translations');
    }
}
