<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_classrooms', function (Blueprint $table) {
            $table->increments('subject_classroom_id');
            $table->integer('subject_id')->index()->unsigned();
            $table->integer('classroom_id')->index()->unsigned();
            $table->integer('academic_term_id')->index()->unsigned();
            $table->integer('exam_status_id')->index()->unsigned()->default(2);

            $table->foreign('subject_id')->references('subject_id')->on(env('ADMIN_DB_DATABASE') . '.subjects')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('classroom_id')->references('classroom_id')->on('classrooms')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->engine = 'InnoDB';
        });

        Schema::create('subject_tutors', function (Blueprint $table) {
            $table->increments('subject_tutor_id');
            $table->integer('tutor_id')->index()->unsigned()->nullable();
            $table->integer('subject_classroom_id')->index()->unsigned();
            $table->timestamps();

            $table->foreign('tutor_id')->references('user_id')->on('users')
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
        Schema::drop('subject_classrooms');
        Schema::drop('subject_tutors');
    }
}
