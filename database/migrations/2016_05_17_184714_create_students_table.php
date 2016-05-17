<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('status', function (Blueprint $table) {
            $table->increments('status_id');
            $table->string('status');
            $table->string('label');
            $table->engine = 'InnoDB';
        });

        Schema::create('students', function (Blueprint $table) {
            $table->increments('student_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('middle_name', 70)->nullable();
            $table->string('student_no', 10)->nullable();
            $table->string('gender', 10);
            $table->date('dob', 10)->nullable();
            $table->string('avatar')->nullable();
            $table->integer('sponsor_id')->index()->unsigned();
            $table->integer('classroom_id')->index()->unsigned();
            $table->integer('status_id')->index()->unsigned()->default(1);
            $table->integer('admitted_term_id')->index()->unsigned();
            $table->integer('lga_id')->index()->unsigned()->nullable();
            $table->integer('created_by')->index()->unsigned();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('student_classes', function (Blueprint $table) {
            $table->increments('student_class_id');
            $table->integer('student_id')->index()->unsigned()->default(1);
            $table->integer('classroom_id')->index()->unsigned();
            $table->integer('academic_year_id')->index()->unsigned();
            $table->timestamps();

            $table->foreign('classroom_id')->references('classroom_id')->on('classrooms')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::connection('admin_mysql')->drop('status');
        Schema::drop('students');
        Schema::drop('student_classes');
    }
}
