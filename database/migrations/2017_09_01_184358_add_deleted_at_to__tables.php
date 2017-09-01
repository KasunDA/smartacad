<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::connection('admin_mysql')->table('schools', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academic_years', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('academic_terms', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::connection('admin_mysql')->create('subject_groups', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::connection('admin_mysql')->create('subjects', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('classgroups', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('classlevels', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('classrooms', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::connection('admin_mysql')->table('schools', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('academic_terms', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::connection('admin_mysql')->create('subject_groups', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::connection('admin_mysql')->create('subjects', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('classgroups', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('classlevels', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

    }
}
