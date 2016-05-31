<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_masters', function (Blueprint $table) {
            $table->increments('class_master_id');
            $table->integer('user_id')->index()->unsigned()->nullable();
            $table->integer('classroom_id')->index()->unsigned();
            $table->integer('academic_year_id')->index()->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('class_masters');
    }
}
