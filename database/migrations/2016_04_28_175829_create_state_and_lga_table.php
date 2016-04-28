<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateAndLgaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('states', function (Blueprint $table) {
            $table->increments('state_id');
            $table->string('state', 150);
            $table->string('state_code', 10);
            $table->timestamps();
        });

        Schema::connection('admin_mysql')->create('lgas', function (Blueprint $table) {
            $table->increments('lga_id');
            $table->string('lga', 150);
            $table->integer('state_id')->unsigned()->index();
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
        Schema::connection('admin_mysql')->drop('states');
        Schema::connection('admin_mysql')->drop('lgas');
    }
}
