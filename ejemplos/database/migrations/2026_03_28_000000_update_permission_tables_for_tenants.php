<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $rolesTable = $tableNames['roles'];
        $modelHasPermissionsTable = $tableNames['model_has_permissions'];
        $modelHasRolesTable = $tableNames['model_has_roles'];

        // roles
        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (!Schema::hasColumn($rolesTable, 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index('roles_tenant_id_index');
            }
        });

        if (Schema::hasColumn($rolesTable, 'team_id')) {
            DB::statement("UPDATE {$rolesTable} SET tenant_id = team_id WHERE tenant_id IS NULL");
        }

        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (Schema::hasColumn($rolesTable, 'team_id')) {
                $table->dropUnique('roles_team_id_name_guard_name_unique');
                $table->dropIndex('roles_team_foreign_key_index');
                $table->dropColumn('team_id');
            }
        });

        Schema::table($rolesTable, function (Blueprint $table) {
            $table->unique(['tenant_id', 'name', 'guard_name'], 'roles_tenant_name_guard_unique');
        });

        // model_has_permissions
        Schema::table($modelHasPermissionsTable, function (Blueprint $table) use ($modelHasPermissionsTable) {
            if (!Schema::hasColumn($modelHasPermissionsTable, 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->index('model_has_permissions_tenant_id_index');
            }
        });

        if (Schema::hasColumn($modelHasPermissionsTable, 'team_id')) {
            DB::statement("UPDATE {$modelHasPermissionsTable} SET tenant_id = team_id WHERE tenant_id IS NULL");
        }
        if (Schema::hasColumn($modelHasPermissionsTable, 'team_id')) {
            DB::statement("ALTER TABLE {$modelHasPermissionsTable} DROP PRIMARY KEY");
        }

        Schema::table($modelHasPermissionsTable, function (Blueprint $table) use ($modelHasPermissionsTable) {
            if (Schema::hasColumn($modelHasPermissionsTable, 'team_id')) {
                $table->dropIndex('model_has_permissions_team_foreign_key_index');
                $table->dropColumn('team_id');
            }
        });

        if (Schema::hasColumn($modelHasPermissionsTable, 'team_id')) {
            DB::statement("ALTER TABLE {$modelHasPermissionsTable} ADD PRIMARY KEY (tenant_id, permission_id, model_id, model_type)");
        }

        // model_has_roles
        Schema::table($modelHasRolesTable, function (Blueprint $table) use ($modelHasRolesTable) {
            if (!Schema::hasColumn($modelHasRolesTable, 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->index('model_has_roles_tenant_id_index');
            }
        });

        if (Schema::hasColumn($modelHasRolesTable, 'team_id')) {
            DB::statement("UPDATE {$modelHasRolesTable} SET tenant_id = team_id WHERE tenant_id IS NULL");
        }
        if (Schema::hasColumn($modelHasRolesTable, 'team_id')) {
            DB::statement("ALTER TABLE {$modelHasRolesTable} DROP PRIMARY KEY");
        }

        Schema::table($modelHasRolesTable, function (Blueprint $table) use ($modelHasRolesTable) {
            if (Schema::hasColumn($modelHasRolesTable, 'team_id')) {
                $table->dropIndex('model_has_roles_team_foreign_key_index');
                $table->dropColumn('team_id');
            }
        });

        if (Schema::hasColumn($modelHasRolesTable, 'team_id')) {
            DB::statement("ALTER TABLE {$modelHasRolesTable} ADD PRIMARY KEY (tenant_id, role_id, model_id, model_type)");
        }
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $rolesTable = $tableNames['roles'];
        $modelHasPermissionsTable = $tableNames['model_has_permissions'];
        $modelHasRolesTable = $tableNames['model_has_roles'];

        // roles
        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (!Schema::hasColumn($rolesTable, 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->index('roles_team_foreign_key_index');
            }
        });

        if (Schema::hasColumn($rolesTable, 'tenant_id')) {
            DB::statement("UPDATE {$rolesTable} SET team_id = tenant_id WHERE team_id IS NULL");
        }

        Schema::table($rolesTable, function (Blueprint $table) use ($rolesTable) {
            if (Schema::hasColumn($rolesTable, 'tenant_id')) {
                $table->dropIndex('roles_tenant_id_index');
                $table->dropUnique('roles_tenant_name_guard_unique');
                $table->dropColumn('tenant_id');
            }
        });

        Schema::table($rolesTable, function (Blueprint $table) {
            $table->unique(['team_id', 'name', 'guard_name'], 'roles_team_name_guard_unique');
        });

        // model_has_permissions
        Schema::table($modelHasPermissionsTable, function (Blueprint $table) use ($modelHasPermissionsTable) {
            if (!Schema::hasColumn($modelHasPermissionsTable, 'team_id')) {
                $table->unsignedBigInteger('team_id')->index('model_has_permissions_team_foreign_key_index');
            }
        });

        if (Schema::hasColumn($modelHasPermissionsTable, 'tenant_id')) {
            DB::statement("UPDATE {$modelHasPermissionsTable} SET team_id = tenant_id WHERE team_id IS NULL");
        }
        if (Schema::hasColumn($modelHasPermissionsTable, 'tenant_id')) {
            DB::statement("ALTER TABLE {$modelHasPermissionsTable} DROP PRIMARY KEY");
        }

        Schema::table($modelHasPermissionsTable, function (Blueprint $table) use ($modelHasPermissionsTable) {
            if (Schema::hasColumn($modelHasPermissionsTable, 'tenant_id')) {
                $table->dropIndex('model_has_permissions_tenant_id_index');
                $table->dropColumn('tenant_id');
            }
        });

        if (Schema::hasColumn($modelHasPermissionsTable, 'tenant_id')) {
            DB::statement("ALTER TABLE {$modelHasPermissionsTable} ADD PRIMARY KEY (team_id, permission_id, model_id, model_type)");
        }

        // model_has_roles
        Schema::table($modelHasRolesTable, function (Blueprint $table) use ($modelHasRolesTable) {
            if (!Schema::hasColumn($modelHasRolesTable, 'team_id')) {
                $table->unsignedBigInteger('team_id')->index('model_has_roles_team_foreign_key_index');
            }
        });

        if (Schema::hasColumn($modelHasRolesTable, 'tenant_id')) {
            DB::statement("UPDATE {$modelHasRolesTable} SET team_id = tenant_id WHERE team_id IS NULL");
        }
        if (Schema::hasColumn($modelHasRolesTable, 'tenant_id')) {
            DB::statement("ALTER TABLE {$modelHasRolesTable} DROP PRIMARY KEY");
        }

        Schema::table($modelHasRolesTable, function (Blueprint $table) use ($modelHasRolesTable) {
            if (Schema::hasColumn($modelHasRolesTable, 'tenant_id')) {
                $table->dropIndex('model_has_roles_tenant_id_index');
                $table->dropColumn('tenant_id');
            }
        });

        if (Schema::hasColumn($modelHasRolesTable, 'tenant_id')) {
            DB::statement("ALTER TABLE {$modelHasRolesTable} ADD PRIMARY KEY (team_id, role_id, model_id, model_type)");
        }
    }
};
