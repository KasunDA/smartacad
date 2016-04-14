<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('password', 150);
            $table->string('email')->unique();
            $table->string('first_name',45);
            $table->string('last_name',45);
            $table->integer('user_type_id')->unsigned()->index();
            $table->integer('verified')->unsigned()->default(0);
            $table->integer('status')->unsigned()->default(1);

            $table->string('gender', 10)->nullable();
            $table->string('phone_no', 15)->nullable();
            $table->date('dob')->nullable();
            $table->string('avatar')->nullable();
            $table->string('verification_code')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
