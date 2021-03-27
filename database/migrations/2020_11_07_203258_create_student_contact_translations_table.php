<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentContactTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_contact_translations', function (Blueprint $table) {
           $table->bigIncrements('id');
           $table->bigInteger('student_contact_id')->unsigned();
           $table->text('current_address')->nullable();
           $table->text('permanent_address')->nullable();
           $table->string('locale');
           $table->unique(['student_contact_id', 'locale']);
           $table->foreign('student_contact_id')->references('id')->on('student_contacts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_contact_translations');
    }
}
