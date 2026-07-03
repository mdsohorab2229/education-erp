<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjectPools = [
            'BCS' => [
                ['name' => 'Data Structures', 'code' => 'CS101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Database Systems', 'code' => 'CS102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Operating Systems', 'code' => 'CS103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Computer Networks', 'code' => 'CS104', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Programming Lab', 'code' => 'CS105', 'credits' => 2.0, 'type' => 'lab'],
                ['name' => 'Web Development', 'code' => 'CS106', 'credits' => 3.0, 'type' => 'practical'],
            ],
            'BIT' => [
                ['name' => 'Introduction to IT', 'code' => 'IT101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Web Technologies', 'code' => 'IT102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Network Security', 'code' => 'IT103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Database Management', 'code' => 'IT104', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'IT Project Management', 'code' => 'IT105', 'credits' => 2.0, 'type' => 'practical'],
            ],
            'BMATH' => [
                ['name' => 'Calculus I', 'code' => 'MATH101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Linear Algebra', 'code' => 'MATH102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Probability & Statistics', 'code' => 'MATH103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Differential Equations', 'code' => 'MATH104', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Mathematical Modelling', 'code' => 'MATH105', 'credits' => 3.0, 'type' => 'practical'],
            ],
            'BSTAT' => [
                ['name' => 'Statistical Methods', 'code' => 'STAT101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Probability Theory', 'code' => 'STAT102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Regression Analysis', 'code' => 'STAT103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Data Visualization', 'code' => 'STAT104', 'credits' => 2.0, 'type' => 'lab'],
            ],
            'BPHY' => [
                ['name' => 'Classical Mechanics', 'code' => 'PHY101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Quantum Mechanics', 'code' => 'PHY102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Electromagnetism', 'code' => 'PHY103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Physics Lab', 'code' => 'PHY104', 'credits' => 2.0, 'type' => 'lab'],
                ['name' => 'Thermodynamics', 'code' => 'PHY105', 'credits' => 3.0, 'type' => 'theory'],
            ],
            'BAS' => [
                ['name' => 'Applied Mathematics', 'code' => 'AS101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Material Science', 'code' => 'AS102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Electronics', 'code' => 'AS103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Applied Physics Lab', 'code' => 'AS104', 'credits' => 2.0, 'type' => 'lab'],
            ],
            'BEL' => [
                ['name' => 'English Poetry', 'code' => 'ENG101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'English Prose', 'code' => 'ENG102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Linguistics', 'code' => 'ENG103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Creative Writing', 'code' => 'ENG104', 'credits' => 2.0, 'type' => 'practical'],
                ['name' => 'Drama & Theatre', 'code' => 'ENG105', 'credits' => 3.0, 'type' => 'theory'],
            ],
            'BBA' => [
                ['name' => 'Principles of Management', 'code' => 'BUS101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Marketing Management', 'code' => 'BUS102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Financial Accounting', 'code' => 'BUS103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Business Ethics', 'code' => 'BUS104', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Business Communication', 'code' => 'BUS105', 'credits' => 2.0, 'type' => 'practical'],
                ['name' => 'Entrepreneurship', 'code' => 'BUS106', 'credits' => 3.0, 'type' => 'theory'],
            ],
            'BACCT' => [
                ['name' => 'Financial Accounting I', 'code' => 'ACC101', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Cost Accounting', 'code' => 'ACC102', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Auditing', 'code' => 'ACC103', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Taxation', 'code' => 'ACC104', 'credits' => 3.0, 'type' => 'theory'],
                ['name' => 'Accounting Software Lab', 'code' => 'ACC105', 'credits' => 2.0, 'type' => 'lab'],
            ],
        ];

        foreach ($subjectPools as $programCode => $subjects) {
            $program = Program::where('code', $programCode)->first();

            foreach ($subjects as $subject) {
                Subject::firstOrCreate(
                    ['program_id' => $program->id, 'code' => $subject['code']],
                    [
                        'name' => $subject['name'],
                        'credits' => $subject['credits'],
                        'type' => $subject['type'],
                    ]
                );
            }
        }
    }
}
