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

            if ($roleName === 'Principal') {
                $role->givePermissionTo([
                    'dashboard-access',
                    'academic-year-list',
                    'department-list',
                    'program-list',
                    'section-list',
                    'subject-list',
                    'shift-list',
                    'group-list',
                    'student-list', 'student-create', 'student-edit',
                    'teacher-list', 'teacher-create', 'teacher-edit',
                    'attendance-list', 'attendance-create', 'attendance-edit',
                    'routine-list',
                    'content-list', 'content-download',
                    'assignment-list',
                    'exam-list',
                    'marks-entry', 'marks-approve',
                ]);
            }

            if ($roleName === 'Department Head') {
                $role->givePermissionTo([
                    'dashboard-access',
                    'department-list',
                    'program-list',
                    'section-list',
                    'subject-list', 'subject-create', 'subject-edit',
                    'shift-list',
                    'group-list',
                    'student-list', 'student-create', 'student-edit',
                    'teacher-list',
                    'attendance-list', 'attendance-create', 'attendance-edit',
                    'routine-list', 'routine-create', 'routine-edit',
                    'content-list', 'content-upload', 'content-edit',
                    'assignment-list', 'assignment-create', 'assignment-edit',
                    'exam-list',
                    'marks-entry',
                ]);
            }

            if ($roleName === 'Teacher') {
                $role->givePermissionTo([
                    'dashboard-access',
                    'student-list',
                    'attendance-list', 'attendance-create', 'attendance-edit', 'attendance-delete',
                    'routine-list',
                    'content-list', 'content-upload', 'content-edit', 'content-delete', 'content-download', 'content-comment',
                    'assignment-list', 'assignment-create', 'assignment-submit', 'assignment-edit', 'assignment-delete', 'assignment-review',
                    'exam-list',
                    'marks-entry',
                ]);
            }

            if ($roleName === 'Student') {
                $role->givePermissionTo([
                    'dashboard-access',
                    'attendance-list',
                    'routine-list',
                    'content-list', 'content-download',
                    'assignment-list', 'assignment-submit',
                    'exam-list',
                ]);
            }

            if ($roleName === 'Parent') {
                $role->givePermissionTo([
                    'dashboard-access',
                    'attendance-list',
                    'routine-list',
                    'assignment-list',
                    'exam-list',
                ]);
            }
        }
    }
}
