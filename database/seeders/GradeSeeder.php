<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['grade_name' => 'A+', 'grade_letter' => 'A+', 'min_mark' => 80, 'max_mark' => 100, 'gpa_point' => 4.00, 'remarks' => 'Outstanding'],
            ['grade_name' => 'A', 'grade_letter' => 'A', 'min_mark' => 75, 'max_mark' => 79, 'gpa_point' => 3.75, 'remarks' => 'Excellent'],
            ['grade_name' => 'A-', 'grade_letter' => 'A-', 'min_mark' => 70, 'max_mark' => 74, 'gpa_point' => 3.50, 'remarks' => 'Very Good'],
            ['grade_name' => 'B+', 'grade_letter' => 'B+', 'min_mark' => 65, 'max_mark' => 69, 'gpa_point' => 3.25, 'remarks' => 'Good'],
            ['grade_name' => 'B', 'grade_letter' => 'B', 'min_mark' => 60, 'max_mark' => 64, 'gpa_point' => 3.00, 'remarks' => 'Satisfactory'],
            ['grade_name' => 'B-', 'grade_letter' => 'B-', 'min_mark' => 55, 'max_mark' => 59, 'gpa_point' => 2.75, 'remarks' => 'Above Average'],
            ['grade_name' => 'C+', 'grade_letter' => 'C+', 'min_mark' => 50, 'max_mark' => 54, 'gpa_point' => 2.50, 'remarks' => 'Average'],
            ['grade_name' => 'C', 'grade_letter' => 'C', 'min_mark' => 45, 'max_mark' => 49, 'gpa_point' => 2.25, 'remarks' => 'Below Average'],
            ['grade_name' => 'C-', 'grade_letter' => 'C-', 'min_mark' => 40, 'max_mark' => 44, 'gpa_point' => 2.00, 'remarks' => 'Poor'],
            ['grade_name' => 'D', 'grade_letter' => 'D', 'min_mark' => 35, 'max_mark' => 39, 'gpa_point' => 1.50, 'remarks' => 'Very Poor'],
            ['grade_name' => 'F', 'grade_letter' => 'F', 'min_mark' => 0, 'max_mark' => 34, 'gpa_point' => 0.00, 'remarks' => 'Fail'],
        ];

        foreach ($grades as $grade) {
            Grade::firstOrCreate(
                ['grade_name' => $grade['grade_name']],
                $grade
            );
        }
    }
}
