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
        Schema::create('order_initiates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true)->index();
            $table->integer('academic_term_id', false, true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 20)->nullable();
            $table->string('status', 20)->default('Not-Paid');
            $table->boolean('paid')->default(0);
            $table->boolean('backend')->default(0);
            $table->decimal('amount', 10, 2)->index()->default(0);
            $table->decimal('tax', 10, 2)->nullable()->default(0);
            $table->integer('student_id', false, true)->index();
            $table->integer('sponsor_id', false, true)->index();
            $table->integer('classroom_id', false, true)->index();
            $table->integer('academic_term_id', false, true)->index();
            $table->integer('order_initiate_id', false, true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->engine = 'InnoDB';
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10, 2)->index();
            $table->integer('quantity', false, true)->index()->nullable();
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

        Schema::create('order_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('comment')->nullable();
            $table->integer('user_id', false, true)->index();
            $table->integer('order_id', false, true)->index();
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
        Schema::drop('order_logs');
        Schema::drop('order_items');
        Schema::drop('orders');
        Schema::drop('order_initiates');
    }
}
