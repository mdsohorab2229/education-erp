<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();
        $subjects = Subject::all();

        $teachers = [
            [
                'first_name' => 'David', 'last_name' => 'Wilson',
                'email' => 'david.wilson@school.edu', 'designation' => 'Professor',
                'departments' => ['CS'],
            ],
            [
                'first_name' => 'Sarah', 'last_name' => 'Johnson',
                'email' => 'sarah.johnson@school.edu', 'designation' => 'Associate Professor',
                'departments' => ['MATH'],
            ],
            [
                'first_name' => 'Michael', 'last_name' => 'Brown',
                'email' => 'michael.brown@school.edu', 'designation' => 'Senior Lecturer',
                'departments' => ['PHY'],
            ],
            [
                'first_name' => 'Emily', 'last_name' => 'Davis',
                'email' => 'emily.davis@school.edu', 'designation' => 'Lecturer',
                'departments' => ['ENG'],
            ],
            [
                'first_name' => 'James', 'last_name' => 'Taylor',
                'email' => 'james.taylor@school.edu', 'designation' => 'Associate Professor',
                'departments' => ['BUS'],
            ],
            [
                'first_name' => 'Jennifer', 'last_name' => 'Anderson',
                'email' => 'jennifer.anderson@school.edu', 'designation' => 'Senior Lecturer',
                'departments' => ['CS', 'MATH'],
            ],
            [
                'first_name' => 'Robert', 'last_name' => 'Thomas',
                'email' => 'robert.thomas@school.edu', 'designation' => 'Lecturer',
                'departments' => ['PHY'],
            ],
            [
                'first_name' => 'Lisa', 'last_name' => 'Jackson',
                'email' => 'lisa.jackson@school.edu', 'designation' => 'Professor',
                'departments' => ['ENG'],
            ],
            [
                'first_name' => 'William', 'last_name' => 'White',
                'email' => 'william.white@school.edu', 'designation' => 'Assistant Professor',
                'departments' => ['BUS'],
            ],
            [
                'first_name' => 'Maria', 'last_name' => 'Garcia',
                'email' => 'maria.garcia@school.edu', 'designation' => 'Senior Lecturer',
                'departments' => ['CS'],
            ],
        ];

        $employeeIndex = 1;

        foreach ($teachers as $teacherData) {
            $deptNames = $teacherData['departments'];
            unset($teacherData['departments']);

            $teacherData['employee_id'] = 'EMP-' . str_pad((string) $employeeIndex, 4, '0', STR_PAD_LEFT);
            $teacherData['phone'] = fake()->unique()->phoneNumber();
            $teacherData['date_of_birth'] = fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d');
            $teacherData['gender'] = fake()->randomElement(['male', 'female']);
            $teacherData['address'] = fake()->address();
            $teacherData['joining_date'] = fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d');
            $teacherData['status'] = 'active';

            $teacher = Teacher::create($teacherData);

            foreach ($deptNames as $deptCode) {
                $dept = $departments->firstWhere('code', $deptCode);
                if ($dept) {
                    $teacher->departments()->attach($dept->id);
                }
            }

            $programCodes = $teacher->departments->flatMap(fn ($d) => $d->programs->pluck('code'));
            $teacherSubjects = $subjects->filter(fn ($s) => $programCodes->contains(
                optional($s->program)->code
            ))->random(min(3, $subjects->count()));

            foreach ($teacherSubjects as $subj) {
                $teacher->subjects()->attach($subj->id);
            }

            $employeeIndex++;
        }
    }
}
