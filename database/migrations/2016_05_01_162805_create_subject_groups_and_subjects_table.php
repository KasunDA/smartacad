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

        Schema::connection('admin_mysql')->create('subjects', function (Blueprint $table) {
            $table->increments('subject_id');
            $table->string('subject');
            $table->string('subject_abbr', 10)->nullable();
            $table->integer('subject_group_id')->index()->unsigned();
            $table->timestamps();

            $table->foreign('subject_group_id')->references('subject_group_id')->on('subject_groups')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::connection('admin_mysql')->create('schools_subjects', function (Blueprint $table) {
            $table->integer('school_id')->unsigned()->index();
            $table->integer('subject_id')->unsigned()->index();

            $table->foreign('school_id')->references('school_id')->on('schools')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['school_id', 'subject_id']);
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
        Schema::connection('admin_mysql')->drop('subjects');
        Schema::connection('admin_mysql')->drop('schools_subjects');
    }
}
