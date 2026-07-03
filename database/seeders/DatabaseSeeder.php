<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            AcademicYearSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            SectionSeeder::class,
            SubjectSeeder::class,
            ShiftSeeder::class,
            GroupSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            ContentDemoSeeder::class,
            ContentDemoSeeder::class,
            AssignmentDemoSeeder::class,
            AttendanceDemoSeeder::class,
            RoutineDemoSeeder::class,
        ]);
    }
}
