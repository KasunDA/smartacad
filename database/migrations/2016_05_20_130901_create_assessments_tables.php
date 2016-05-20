<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_setups', function (Blueprint $table) {
            $table->increments('assessment_setup_id');
            $table->tinyInteger('assessment_no');
            $table->integer('classgroup_id')->index()->unsigned();
            $table->integer('academic_term_id')->index()->unsigned();
            $table->engine = 'InnoDB';

        });

        Schema::create('assessment_setup_details', function (Blueprint $table) {
            $table->increments('assessment_setup_detail_id');
            $table->tinyInteger('number');
            $table->float('weight_point')->unsigned();
            $table->integer('percentage')->unsigned();
            $table->integer('assessment_setup_id')->index()->unsigned();
            $table->date('submission_date')->nullable();
            $table->string('description')->nullable();

            $table->foreign('assessment_setup_id')->references('assessment_setup_id')
                ->on('assessment_setups')->onUpdate('cascade')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->increments('assessment_id');
            $table->integer('subject_classroom_id')->index()->unsigned();
            $table->integer('assessment_setup_detail_id')->index()->unsigned();
            $table->integer('marked')->index()->unsigned()->default(2);

            $table->foreign('assessment_setup_detail_id')->references('assessment_setup_detail_id')
                ->on('assessment_setup_details')->onUpdate('cascade')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });

        Schema::create('assessment_details', function (Blueprint $table) {
            $table->increments('assessment_detail_id');
            $table->integer('student_id')->index()->unsigned();
            $table->float('score')->unsigned()->default(0);
            $table->integer('assessment_id')->index()->unsigned();

            $table->foreign('assessment_id')->references('assessment_id')->on('assessments')->onUpdate('cascade')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assessment_details');
        Schema::drop('assessments');
        Schema::drop('assessment_setup_details');
        Schema::drop('assessment_setups');
    }
}
