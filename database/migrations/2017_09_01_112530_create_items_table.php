<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_type', 60);
            $table->softDeletes();

            $table->engine = 'InnoDB';
        });

        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80);
            $table->text('description')->nullable();
            $table->boolean('status')->index()->default(1);
            $table->integer('item_type_id', false, true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            
            $table->foreign('item_type_id')
                ->references('id')
                ->on('item_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });

        Schema::create('item_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10, 2)->index();
            $table->integer('item_id', false, true)->index();
            $table->integer('classlevel_id', false, true)->index();
            $table->integer('academic_year_id', false, true)->index();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        Schema::create('item_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10, 2)->index()->nullable()->default(0);
            $table->integer('item_id', false, true)->index();
            $table->integer('student_id', false, true)->index()->nullable();
            $table->integer('classroom_id', false, true)->index()->nullable();
            $table->integer('academic_term_id', false, true)->index();
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
        Schema::drop('item_variables');
        Schema::drop('item_quotes');
        Schema::drop('items');
        Schema::drop('item_types');
    }
}
