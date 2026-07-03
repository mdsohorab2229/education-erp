<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Principal',
            'Department Head',
            'Teacher',
            'Student',
            'Parent',
        ];

        $allPermissions = Permission::pluck('name')->toArray();

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (in_array($roleName, ['Super Admin', 'Admin'])) {
                $role->syncPermissions($allPermissions);
            }

            $attendancePermissions = Permission::whereIn('name', [
                'attendance-list', 'attendance-create', 'attendance-edit', 'attendance-delete',
            ])->pluck('name')->toArray();

            if ($roleName === 'Teacher') {
                $role->givePermissionTo($attendancePermissions);
            }

            if ($roleName === 'Department Head') {
                $role->givePermissionTo(['attendance-list', 'attendance-create', 'attendance-edit']);
            }
        }
    }
}
