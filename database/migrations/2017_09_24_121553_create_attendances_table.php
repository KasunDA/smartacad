<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('classroom_id', false, true)->index();
            $table->integer('academic_term_id', false, true)->index();
            $table->integer('user_id', false, true)->index();
            $table->date('attendance_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->engine = 'InnoDB';
        });

        Schema::create('attendance_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id', false, true)->index();
            $table->tinyInteger('status', false, true)->index()->default(0);
            $table->string('reason')->nullable();
            $table->integer('attendance_id', false, true)->index();
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
        Schema::drop('attendance_details');
        Schema::drop('attendances');
    }
}
