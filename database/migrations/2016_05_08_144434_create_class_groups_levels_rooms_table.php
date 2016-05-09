<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassGroupsLevelsRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classgroups', function (Blueprint $table) {
            $table->increments('classgroup_id');
            $table->string('classgroup');
            $table->integer('ca_weight_point')->unsigned()->default(0)->nullable();
            $table->integer('exam_weight_point')->unsigned()->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('classlevels', function (Blueprint $table) {
            $table->increments('classlevel_id');
            $table->string('classlevel');
            $table->integer('classgroup_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('classgroup_id')->references('classgroup_id')->on('classgroups')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->increments('classroom_id');
            $table->string('classroom');
            $table->integer('class_size')->nullable();
            $table->integer('class_status')->unsigned()->default(1);
            $table->integer('classlevel_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('classlevel_id')->references('classlevel_id')->on('classlevels')
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
        Schema::drop('classgroups');
        Schema::drop('classlevels');
        Schema::drop('classrooms');
    }
}
