<?php

namespace Database\Seeders;

use App\Models\Tenancy\Tenant;
use App\Models\Tenancy\TenantUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AuthTestSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner@kocert.com'],
            [
                'name' => 'Owner Admin',
                'password' => Hash::make('Password123!'),
            ]
        );

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-certificadora'],
            [
                'name' => 'Demo Certificadora',
                'plan' => 'basic',
                'active' => true,
            ]
        );

        $tenantAdmin = TenantUser::firstOrCreate(
            ['email' => 'admin@demo-certificadora.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin Demo',
                'password' => Hash::make('Password123!'),
                'active' => true,
            ]
        );

        $tenantOperator = TenantUser::firstOrCreate(
            ['email' => 'operador@demo-certificadora.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Operador Demo',
                'password' => Hash::make('Password123!'),
                'active' => true,
            ]
        );

        $tenantViewer = TenantUser::firstOrCreate(
            ['email' => 'viewer@demo-certificadora.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Viewer Demo',
                'password' => Hash::make('Password123!'),
                'active' => true,
            ]
        );

        // Ensure roles/permissions exist (owner + tenant)
        $this->call(RolePermissionSeeder::class);

        app(PermissionRegistrar::class)->setPermissionsTeamId(0);
        $ownerRole = Role::query()
            ->where('name', 'owner-admin')
            ->where('guard_name', 'owner')
            ->first();

        if ($ownerRole) {
            $owner->syncRoles([$ownerRole]);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        $tenantAdminRole = Role::query()
            ->where('name', 'tenant-admin')
            ->where('guard_name', 'tenant')
            ->where('tenant_id', $tenant->id)
            ->first();
        $tenantOperatorRole = Role::query()
            ->where('name', 'tenant-operator')
            ->where('guard_name', 'tenant')
            ->where('tenant_id', $tenant->id)
            ->first();
        $tenantViewerRole = Role::query()
            ->where('name', 'tenant-viewer')
            ->where('guard_name', 'tenant')
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($tenantAdminRole) {
            $tenantAdmin->syncRoles([$tenantAdminRole]);
        }
        if ($tenantOperatorRole) {
            $tenantOperator->syncRoles([$tenantOperatorRole]);
        }
        if ($tenantViewerRole) {
            $tenantViewer->syncRoles([$tenantViewerRole]);
        }
    }
}
