<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentPromotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentPromotion>
 */
class StudentPromotionFactory extends Factory
{
    protected $model = StudentPromotion::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'from_academic_year_id' => AcademicYear::factory(),
            'to_academic_year_id' => AcademicYear::factory(),
            'from_section_id' => Section::factory(),
            'to_section_id' => Section::factory(),
            'promoted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
