<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectGroupsAndSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('subject_groups', function (Blueprint $table) {
            $table->increments('subject_group_id');
            $table->string('subject_group');
        });

        Schema::connection('admin_mysql')->create('school_subjects', function (Blueprint $table) {
            $table->increments('school_subject_id');
            $table->string('school_subject');
            $table->string('school_subject_abbr', 10)->nullable();
            $table->integer('subject_group_id')->index()->unsigned();
            $table->timestamps();

            $table->foreign('subject_group_id')->references('subject_group_id')->on('subject_groups')
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
        Schema::connection('admin_mysql')->drop('subject_groups');
        Schema::connection('admin_mysql')->drop('school_subjects');
    }
}
