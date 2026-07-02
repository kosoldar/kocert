<?php

namespace Database\Seeders;

use App\Models\Tenancy\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedOwnerRoles();
        $this->seedTenantRoles();
    }

    private function seedOwnerRoles(): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId(0);

        $permissions = [
            'owners.manage',
            'tenants.manage',
            'billing.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'owner',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'owner-admin',
            'guard_name' => 'owner',
            'tenant_id' => 0,
        ]);

        $admin->syncPermissions($permissions);
    }

    private function seedTenantRoles(): void
    {
        $permissions = [
            'certificates.view',
            'certificates.manage',
            'clients.manage',
            'inspectors.manage',
            'welders.manage',
            'wps.manage',
            'users.manage',
        ];

        $rolePermissions = [
            'tenant-admin' => $permissions,
            'tenant-operator' => [
                'certificates.view',
                'certificates.manage',
                'clients.manage',
                'inspectors.manage',
                'welders.manage',
                'wps.manage',
            ],
            'tenant-viewer' => [
                'certificates.view',
            ],
        ];

        Tenant::query()->each(function (Tenant $tenant) use ($permissions, $rolePermissions) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'tenant',
                ]);
            }

            foreach ($rolePermissions as $roleName => $rolePerms) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'tenant',
                    'tenant_id' => $tenant->id,
                ]);

                $role->syncPermissions($rolePerms);
            }
        });
    }
}
