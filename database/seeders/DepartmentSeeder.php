<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CS', 'description' => 'Department of Computer Science and Information Technology'],
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Department of Mathematics and Statistics'],
            ['name' => 'Physics', 'code' => 'PHY', 'description' => 'Department of Physics and Applied Sciences'],
            ['name' => 'English', 'code' => 'ENG', 'description' => 'Department of English Language and Literature'],
            ['name' => 'Business Administration', 'code' => 'BUS', 'description' => 'Department of Business and Management Studies'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
