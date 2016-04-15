<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_headers', function (Blueprint $table) {
            $table->increments('menu_header_id');
            $table->string('menu_header', 150);
            $table->integer('active')->unsigned()->default(1);
            $table->integer('sequence')->unsigned();
            $table->integer('type')->unsigned()->default(1);
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->string('menu', 150);
            $table->string('menu_url', 150)->nullable();
            $table->integer('active')->unsigned()->default(1);
            $table->integer('sequence')->unsigned();
            $table->integer('type')->unsigned()->default(1);
            $table->string('icon');
            $table->integer('menu_header_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('menu_item_id');
            $table->string('menu_item', 150);
            $table->string('menu_item_url', 150);
            $table->string('menu_item_icon', 100);
            $table->integer('active')->unsigned()->default(1);
            $table->string('sequence');
            $table->integer('type')->unsigned()->default(1);
            $table->integer('menu_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('sub_menu_items', function (Blueprint $table) {
            $table->increments('sub_menu_item_id');
            $table->string('sub_menu_item', 150);
            $table->string('sub_menu_item_url', 150);
            $table->string('sub_menu_item_icon', 100);
            $table->integer('active')->unsigned()->default(1);
            $table->string('sequence');
            $table->integer('type')->unsigned()->default(1);
            $table->integer('menu_item_id')->unsigned()->index();
            $table->timestamps();
        });

        Schema::create('sub_most_menu_items', function (Blueprint $table) {
            $table->increments('sub_most_menu_item_id');
            $table->string('sub_most_menu_item', 150);
            $table->string('sub_most_menu_item_url', 150);
            $table->string('sub_most_menu_item_icon', 100);
            $table->integer('active')->unsigned()->default(1);
            $table->string('sequence');
            $table->integer('type')->unsigned()->default(1);
            $table->integer('sub_menu_item_id')->unsigned()->index();
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
        Schema::drop('menu_headers');
        Schema::drop('menus');
        Schema::drop('menu_items');
        Schema::drop('sub_menu_items');
        Schema::drop('sub_most_menu_items');
    }
}
