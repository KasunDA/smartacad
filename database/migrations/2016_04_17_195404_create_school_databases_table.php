<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolDatabasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('school_databases', function (Blueprint $table) {
            $table->increments('school_database_id');
            $table->string('host', 150);
            $table->string('database', 225);
            $table->string('username', 20);
            $table->string('password', 150)->nullable();
            $table->integer('school_id')->index()->unsigned();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('admin_mysql')->drop('school_databases');
    }
}
