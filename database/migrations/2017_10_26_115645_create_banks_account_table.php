<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection('admin_mysql')->hasTable('banks')) {
            Schema::connection('admin_mysql')->create('banks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('code', 10);
                $table->tinyInteger('active', false, true)->index()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::connection('admin_mysql')->hasTable('school_banks')) {
            Schema::connection('admin_mysql')->create('school_banks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('account_name');
                $table->string('account_number');
                $table->tinyInteger('active', false, true)->index()->default(1);
                $table->integer('bank_id', false, true)->index();
                $table->integer('classgroup_id', false, true)->index();
                $table->integer('school_id', false, true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection('admin_mysql')->hasTable('school_banks')) {
            Schema::connection('admin_mysql')->drop('school_banks');
        }
        if (Schema::connection('admin_mysql')->hasTable('banks')) {
            Schema::connection('admin_mysql')->drop('banks');
        }
    }
}
