<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => ['access'],
            'role' => ['list', 'create', 'edit', 'delete'],
            'permission' => ['list', 'create', 'edit', 'delete'],
            'academic-year' => ['list', 'create', 'edit', 'delete'],
            'department' => ['list', 'create', 'edit', 'delete'],
            'program' => ['list', 'create', 'edit', 'delete'],
            'section' => ['list', 'create', 'edit', 'delete'],
            'subject' => ['list', 'create', 'edit', 'delete'],
            'shift' => ['list', 'create', 'edit', 'delete'],
            'group' => ['list', 'create', 'edit', 'delete'],
            'student' => ['list', 'create', 'edit', 'delete'],
            'teacher' => ['list', 'create', 'edit', 'delete'],
            'attendance' => ['list', 'create', 'edit', 'delete'],
            'routine' => ['list', 'create', 'edit', 'delete'],
            'content' => ['list', 'upload', 'edit', 'delete', 'download', 'comment'],
            'assignment' => ['list', 'create', 'submit', 'edit', 'delete', 'review'],
            'exam' => ['list', 'create', 'edit', 'delete'],
            'marks' => ['entry', 'approve'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}-{$action}"]);
            }
        }
    }
}
