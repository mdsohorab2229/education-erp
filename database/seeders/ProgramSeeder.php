<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['department_code' => 'CS', 'name' => 'Bachelor of Computer Science', 'code' => 'BCS', 'duration_years' => 4],
            ['department_code' => 'CS', 'name' => 'Bachelor of Information Technology', 'code' => 'BIT', 'duration_years' => 4],
            ['department_code' => 'MATH', 'name' => 'Bachelor of Mathematics', 'code' => 'BMATH', 'duration_years' => 3],
            ['department_code' => 'MATH', 'name' => 'Bachelor of Statistics', 'code' => 'BSTAT', 'duration_years' => 3],
            ['department_code' => 'PHY', 'name' => 'Bachelor of Physics', 'code' => 'BPHY', 'duration_years' => 3],
            ['department_code' => 'PHY', 'name' => 'Bachelor of Applied Sciences', 'code' => 'BAS', 'duration_years' => 4],
            ['department_code' => 'ENG', 'name' => 'Bachelor of English Literature', 'code' => 'BEL', 'duration_years' => 3],
            ['department_code' => 'BUS', 'name' => 'Bachelor of Business Administration', 'code' => 'BBA', 'duration_years' => 4],
            ['department_code' => 'BUS', 'name' => 'Bachelor of Accounting', 'code' => 'BACCT', 'duration_years' => 4],
        ];

        foreach ($programs as $prog) {
            $department = Department::where('code', $prog['department_code'])->first();

            Program::firstOrCreate(
                ['code' => $prog['code']],
                [
                    'department_id' => $department->id,
                    'name' => $prog['name'],
                    'duration_years' => $prog['duration_years'],
                    'description' => "{$prog['name']} program ({$prog['duration_years']} years)",
                ]
            );
        }
    }
}
