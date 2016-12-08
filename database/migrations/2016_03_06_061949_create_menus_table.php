<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('menus', function(Blueprint $table) {
      // These columns are needed for Baum's Nested Set implementation to work.
      // Column names may be changed, but they *must* all exist and be modified
      // in the model.
      // Take a look at the model scaffold comments for details.
      // We add indexes on parent_id, lft, rgt columns by default.
      $table->increments('menu_id');
      $table->integer('parent_id')->nullable()->index();
      $table->integer('lft')->nullable()->index();
      $table->integer('rgt')->nullable()->index();
      $table->integer('depth')->nullable();

      // Add needed columns here (f.ex: name, slug, path, etc.)
      $table->string('name', 255);
      $table->string('url', 150)->nullable();
      $table->integer('active')->unsigned()->default(1);
      $table->integer('sequence')->unsigned()->index()->default(1);
      $table->integer('type')->unsigned()->default(1)->index();;
      $table->string('icon')->nullable();

      $table->timestamps();
    });

    Schema::create('menus_roles', function (Blueprint $table) {
      $table->integer('menu_id')->nullable()->unsigned()->index();
      $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('cascade')->onUpdate('cascade');;

      $table->integer('role_id')->unsigned()->index();
      $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade')->onUpdate('cascade');;
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('menus_roles');
    Schema::dropIfExists('menus');
  }

}
