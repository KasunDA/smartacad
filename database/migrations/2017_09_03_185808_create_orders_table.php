<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status', 20);
            $table->string('number', 20);
            $table->boolean('paid')->default(0);
            $table->boolean('backend')->default(0);
            $table->decimal('amount', 10, 2)->index();
            $table->decimal('tax', 10, 2)->index();
            $table->integer('student_id', false, true)->index();
            $table->integer('sponsor_id', false, true)->index();
            $table->integer('classroom_id', false, true)->index();
            $table->integer('academic_term_id', false, true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->engine = 'InnoDB';
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10, 2)->index();
            $table->integer('quantity', false, true)->index();
            $table->integer('order_id', false, true)->index();
            $table->integer('item_id', false, true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_items');
        Schema::drop('orders');
    }
}
