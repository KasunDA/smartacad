<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('role_id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->integer('user_type_id')->index()->unsigned();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('user_id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);

            $table->engine = 'InnoDB';
        });

        // Create table for storing permissions
        if (!Schema::connection('admin_mysql')->hasTable('permissions')) {
            Schema::connection('admin_mysql')->create('permissions', function (Blueprint $table) {
                $table->increments('permission_id');
                $table->string('name')->unique();
                $table->string('display_name')->nullable();
                $table->string('description')->nullable();
                $table->string('uri')->nullable();
                $table->timestamps();

                $table->engine = 'InnoDB';
            });
        }

        // Create table for associating permissions to roles (Many-to-Many)
        if (!Schema::connection('admin_mysql')->hasTable('permission_role')) {
            Schema::connection('admin_mysql')->create('permission_role', function (Blueprint $table) {
                $table->integer('permission_id')->unsigned();
                $table->integer('role_id')->unsigned();

                $table->foreign('permission_id')->references('permission_id')->on('permissions')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('role_id')->references('role_id')->on('roles')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->primary(['permission_id', 'role_id']);

                $table->engine = 'InnoDB';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        if (Schema::connection('admin_mysql')->hasTable('permission_role')) {
            Schema::connection('admin_mysql')->drop('permission_role');
        }
        
        if (Schema::connection('admin_mysql')->hasTable('permissions')) {
            Schema::connection('admin_mysql')->drop('permissions');
        }
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}
