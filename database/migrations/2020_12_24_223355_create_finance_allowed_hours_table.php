<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceAllowedHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_allowed_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_year_id')->unsigned()->nullable();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->bigInteger('finance_account_id')->unsigned()->nullable();

            $table->integer('hours')->nullable();

            $table->foreign('study_year_id')
                ->references('id')->on('study_years')->onDelete('set null');
            $table->foreign('semester_id')
                ->references('id')->on('semesters')->onDelete('set null');
            $table->foreign('finance_account_id')
                ->references('id')->on('finance_accounts')->onDelete('set null');

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
        Schema::dropIfExists('finance_allowed_hours');
    }
}
