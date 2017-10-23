<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10)->index()->default(0);
            $table->integer('order_id', false, true)->index();
            $table->integer('user_id', false, true)->index();
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
        Schema::drop('part_payments');
    }
}
