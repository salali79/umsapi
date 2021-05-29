<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicStatusTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_status_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('academic_status_id')->unsigned();
            $table->string('name')->nullable();

            $table->string('locale');
            $table->unique(['academic_status_id', 'locale']);
            $table->foreign('academic_status_id')
                ->references('id')
                ->on('academic_statuses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_status_translations');
    }
}
