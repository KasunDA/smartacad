<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('schools', function (Blueprint $table) {
            $table->increments('schools_id');
            $table->string('name', 150);
            $table->string('full_name', 150);
            $table->string('motto', 10)->nullable();
            $table->string('website', 10)->nullable();
            $table->string('address', 15)->nullable();
            $table->string('logo', 10)->nullable();
            $table->string('admin_id', 10)->nullable();
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
        Schema::drop('schools');
    }
}
