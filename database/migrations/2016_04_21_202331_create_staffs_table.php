<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->increments('staff_id');
            $table->string('staff_no', 20);
            $table->string('first_name', 50);
            $table->string('other_name', 150);
            $table->string('email', 150)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone_no', 20)->nullable();
            $table->string('phone_no2', 20)->nullable();
            $table->text('address')->nullable();
            $table->integer('lga_id')->index()->unsigned()->nullable();
            $table->integer('salutation_id')->index()->unsigned()->nullable();
            $table->integer('created_by')->index()->unsigned();
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
        Schema::drop('staffs');
    }
}
