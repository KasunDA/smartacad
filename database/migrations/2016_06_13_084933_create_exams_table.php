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
        });

        Schema::create('exam_details', function (Blueprint $table) {
            $table->increments('exam_detail_id');
            $table->integer('exam_id')->unsigned()->index();
            $table->integer('student_id')->unsigned()->index();
            $table->float('ca', 5, 2)->unsigned()->default(0.0);
            $table->float('exam', 5, 2)->unsigned()->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('exams');
        Schema::drop('exam_details');
    }
}
