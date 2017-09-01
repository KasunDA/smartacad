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
        if (!Schema::hasColumn('user_types', 'deleted_at')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('academic_years', 'deleted_at')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('academic_terms', 'deleted_at')) {
            Schema::table('academic_terms', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('classgroups', 'deleted_at')) {
            Schema::table('classgroups', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('classlevels', 'deleted_at')) {
            Schema::table('classlevels', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('classrooms', 'deleted_at')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('students', 'deleted_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::connection('admin_mysql')->hasColumn('schools', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('schools', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::connection('admin_mysql')->hasColumn('subject_groups', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('subject_groups', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::connection('admin_mysql')->hasColumn('subjects', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('subjects', function (Blueprint $table) {
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
        if (Schema::hasColumn('user_types', 'deleted_at')) {
            Schema::table('user_types', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('academic_years', 'deleted_at')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('academic_terms', 'deleted_at')) {
            Schema::table('academic_terms', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('classgroups', 'deleted_at')) {
            Schema::table('classgroups', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('classlevels', 'deleted_at')) {
            Schema::table('classlevels', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('classrooms', 'deleted_at')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('students', 'deleted_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::connection('admin_mysql')->hasColumn('schools', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('schools', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::connection('admin_mysql')->hasColumn('subject_groups', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('subject_groups', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::connection('admin_mysql')->hasColumn('subjects', 'deleted_at')) {
            Schema::connection('admin_mysql')->table('subjects', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }

    }
}
