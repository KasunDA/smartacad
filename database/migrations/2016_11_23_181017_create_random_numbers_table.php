<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRandomNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->increments('pin_id');
            $table->string('pin', 20)->index()->unique();
            $table->string('serial', 20)->index()->unique();
            $table->engine = 'InnoDB';
        });

        Schema::create('pin_numbers', function (Blueprint $table) {
            $table->increments('pin_number_id');
            $table->string('pin_number', 20)->index()->unique();
            $table->string('serial_number', 20)->index()->unique();
            $table->smallInteger('status')->index()->unsigned()->default(1);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('result_checkers', function (Blueprint $table) {
            $table->increments('result_checker_id');
            $table->integer('student_id')->index()->unsigned();
            $table->integer('classroom_id')->index()->unsigned();
            $table->integer('academic_term_id')->index()->unsigned();
            $table->integer('pin_number_id')->index()->unsigned();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('academic_term_id')->on('academic_terms')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pin_number_id')->references('pin_number_id')->on('pin_numbers')
                ->onUpdate('cascade')->onDelete('cascade');
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
        Schema::drop('result_checkers');
        Schema::drop('pin_numbers');
        Schema::drop('pins');
    }
}
