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
            $table->increments('school_id');
            $table->string('name', 150);
            $table->string('full_name', 225);
            $table->string('phone_no', 15);
            $table->string('phone_no2', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('db_name', 150);
            $table->string('motto', 150)->nullable();
            $table->integer('setup')->index()->default(1);
            $table->string('website', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('logo', 50)->nullable();
            $table->integer('admin_id')->nullable()->index()->unsigned();
            $table->integer('status_id')->default(1)->index()->unsigned();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::connection('admin_mysql')->drop('schools');
    }
}
