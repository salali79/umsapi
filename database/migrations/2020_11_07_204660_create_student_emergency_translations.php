<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentEmergencyTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_emergency_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_emergency_id')->unsigned();

            $table->string('name')->nullable();
            $table->string('relationship')->nullable();
            $table->string('address')->nullable();
            $table->string('locale');

            $table->unique(['student_emergency_id','locale'],'stud_emergency_locale');
            $table->foreign('student_emergency_id')->references('id')->on('student_emergencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_emergen_translations');
    }
}
