<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->increments('sponsor_id');
            $table->string('titles', 150);
            $table->string('address', 150);
            $table->string('city', 150);
            $table->string('state_id', 150);
            $table->string('country_id', 150);
            $table->string('user_id', 150);
            $table->string('school_id', 150);
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
        Schema::drop('sponsors');
    }
}
