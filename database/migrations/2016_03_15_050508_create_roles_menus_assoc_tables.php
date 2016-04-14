<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesMenusAssocTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_menu_headers', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->integer('menu_header_id')->nullable()->unsigned()->index();
            $table->foreign('menu_header_id')->references('menu_header_id')->on('menu_headers')->onDelete('cascade');
        });

        Schema::create('roles_menus', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->integer('menu_id')->nullable()->unsigned()->index();
            $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('cascade');
        });

        Schema::create('roles_menu_items', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->integer('menu_item_id')->nullable()->unsigned()->index();
            $table->foreign('menu_item_id')->references('menu_item_id')->on('menu_items')->onDelete('cascade');
        });

        Schema::create('roles_sub_menu_items', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->integer('sub_menu_item_id')->nullable()->unsigned()->index();
            $table->foreign('sub_menu_item_id')->references('sub_menu_item_id')->on('sub_menu_items')->onDelete('cascade');
        });

        Schema::create('roles_sub_most_menu_items', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');

            $table->integer('sub_most_menu_item_id')->nullable()->unsigned()->index();
            $table->foreign('sub_most_menu_item_id')->references('sub_most_menu_item_id')->on('sub_most_menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles_menu_headers');
        Schema::drop('roles_menus');
        Schema::drop('roles_menu_items');
        Schema::drop('roles_sub_menu_items');
        Schema::drop('roles_sub_most_menu_items');
    }
}
