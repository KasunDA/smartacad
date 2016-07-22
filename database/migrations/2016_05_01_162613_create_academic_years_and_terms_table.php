<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicYearsAndTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->increments('academic_year_id');
            $table->string('academic_year', 100);
            $table->integer('status')->index()->unsigned()->default(2);
            $table->timestamps();
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->increments('academic_term_id');
            $table->string('academic_term', 100);
            $table->integer('status')->index()->unsigned()->default(2);
            $table->integer('academic_year_id')->index()->unsigned();
            $table->integer('term_type_id')->index()->unsigned();
            $table->date('term_begins')->nullable();
            $table->date('term_ends')->nullable();
//            $table->integer('exam_status_id')->index()->unsigned()->default(2);
//            $table->integer('exam_setup_by')->index()->unsigned()->nullable();
//            $table->date('exam_setup_date')->nullable();
            $table->timestamps();

            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('academic_years');
        Schema::drop('academic_terms');
    }
}
