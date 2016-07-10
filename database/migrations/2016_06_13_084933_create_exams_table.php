<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('exam_id');
            $table->integer('subject_classroom_id')->unsigned()->index();
            $table->integer('marked')->unsigned()->index()->default(2);

            $table->foreign('subject_classroom_id')->references('subject_classroom_id')->on('subject_classrooms')->onUpdate('cascade')->onDelete('cascade');
            $table->engine = 'InnoDB';
        });

        Schema::create('exam_details', function (Blueprint $table) {
            $table->increments('exam_detail_id');
            $table->integer('exam_id')->unsigned()->index();
            $table->integer('student_id')->unsigned()->index();
            $table->float('ca', 5, 2)->unsigned()->default(0.0);
            $table->float('exam', 5, 2)->unsigned()->default(0.0);

            $table->foreign('student_id')->references('student_id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::drop('exam_details');
        Schema::drop('exams');
    }
}
