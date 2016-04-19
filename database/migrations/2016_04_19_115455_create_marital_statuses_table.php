<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaritalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('admin_mysql')->create('marital_statuses', function (Blueprint $table) {
            $table->increments('marital_status_id');
            $table->string('marital_status', 150);
            $table->string('marital_status_abbr', 150);
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
        Schema::connection('admin_mysql')->drop('marital_statuses');
    }
}
