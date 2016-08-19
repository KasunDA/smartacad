<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('domain_id');
            $table->string('domain', 100);
            $table->engine = 'InnoDB';
        });

        Schema::create('domain_assessments', function (Blueprint $table) {
            $table->increments('domain_assessment_id');
            $table->integer('student_id')->unsigned()->index();
            $table->integer('academic_term_id')->unsigned()->index();
            $table->engine = 'InnoDB';
        });

        Schema::create('domain_details', function (Blueprint $table) {
            $table->increments('domain_detail_id');
            $table->integer('domain_id')->unsigned()->index();
            $table->integer('domain_assessment_id')->unsigned()->index();
            $table->integer('option')->unsigned()->index()->default(0);
            $table->engine = 'InnoDB';
        });

        Schema::create('remarks', function (Blueprint $table) {
            $table->increments('remark_id');
            $table->string('class_teacher')->nullable();
            $table->string('principal')->nullable();
            $table->integer('student_id')->unsigned()->index();
            $table->integer('academic_term_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->timestamps();
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
        Schema::drop('domain_details');
        Schema::drop('domain_assessments');
        Schema::drop('domains');
        Schema::drop('remarks');
    }
}
