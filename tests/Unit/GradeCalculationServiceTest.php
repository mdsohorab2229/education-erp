<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\GradeNotFoundException;
use App\Interfaces\Repositories\GradeRepositoryInterface;
use App\Models\Grade;
use App\Services\GradeCalculationService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class GradeCalculationServiceTest extends TestCase
{
    /** @return Grade[] */
    private function defaultGrades(): array
    {
        $data = [
            ['id' => 1,  'grade_name' => 'A+', 'grade_letter' => 'A+', 'min_mark' => 80,  'max_mark' => 100, 'gpa_point' => 4.00],
            ['id' => 2,  'grade_name' => 'A',  'grade_letter' => 'A',  'min_mark' => 75,  'max_mark' => 79,  'gpa_point' => 3.75],
            ['id' => 3,  'grade_name' => 'A-', 'grade_letter' => 'A-', 'min_mark' => 70,  'max_mark' => 74,  'gpa_point' => 3.50],
            ['id' => 4,  'grade_name' => 'B+', 'grade_letter' => 'B+', 'min_mark' => 65,  'max_mark' => 69,  'gpa_point' => 3.25],
            ['id' => 5,  'grade_name' => 'B',  'grade_letter' => 'B',  'min_mark' => 60,  'max_mark' => 64,  'gpa_point' => 3.00],
            ['id' => 6,  'grade_name' => 'B-', 'grade_letter' => 'B-', 'min_mark' => 55,  'max_mark' => 59,  'gpa_point' => 2.75],
            ['id' => 7,  'grade_name' => 'C+', 'grade_letter' => 'C+', 'min_mark' => 50,  'max_mark' => 54,  'gpa_point' => 2.50],
            ['id' => 8,  'grade_name' => 'C',  'grade_letter' => 'C',  'min_mark' => 45,  'max_mark' => 49,  'gpa_point' => 2.25],
            ['id' => 9,  'grade_name' => 'C-', 'grade_letter' => 'C-', 'min_mark' => 40,  'max_mark' => 44,  'gpa_point' => 2.00],
            ['id' => 10, 'grade_name' => 'D',  'grade_letter' => 'D',  'min_mark' => 35,  'max_mark' => 39,  'gpa_point' => 1.50],
            ['id' => 11, 'grade_name' => 'F',  'grade_letter' => 'F',  'min_mark' => 0,   'max_mark' => 34,  'gpa_point' => 0.00],
        ];

        return array_map(fn (array $attrs): Grade => (function () use ($attrs): Grade {
            $g = new Grade();
            $g->setRawAttributes($attrs);
            return $g;
        })(), $data);
    }

    private function createService(array $grades): GradeCalculationService
    {
        $repo = $this->createMock(GradeRepositoryInterface::class);
        $repo->method('allOrdered')->willReturn(new Collection($grades));

        return new GradeCalculationService($repo);
    }

    // ---- Boundary: 100 (top of A+) ----

    public function test_calculate_100_returns_a_plus(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(100);

        $this->assertSame('A+', $result['grade_letter']);
        $this->assertSame('A+', $result['grade_name']);
        $this->assertEquals(4.00, $result['gpa']);
    }

    // ---- Boundary: 80 (bottom of A+) ----

    public function test_calculate_80_returns_a_plus(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(80);

        $this->assertSame('A+', $result['grade_letter']);
    }

    // ---- Boundary: 79.99 (gap between A max=79 and A+ min=80) ----

    public function test_calculate_79_99_throws_grade_not_found(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculate(79.99);
    }

    // ---- Boundary: 70 (bottom of A-) ----

    public function test_calculate_70_returns_a_minus(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(70);

        $this->assertSame('A-', $result['grade_letter']);
    }

    // ---- Boundary: 69.99 (gap between B+ max=69 and A- min=70) ----

    public function test_calculate_69_99_throws_grade_not_found(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculate(69.99);
    }

    // ---- Boundary: 40 (bottom of C-) ----

    public function test_calculate_40_returns_c_minus(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(40);

        $this->assertSame('C-', $result['grade_letter']);
    }

    // ---- Boundary: 39.99 (gap between D max=39 and C- min=40) ----

    public function test_calculate_39_99_throws_grade_not_found(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculate(39.99);
    }

    // ---- Boundary: 0 (bottom of F) ----

    public function test_calculate_0_returns_f(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(0);

        $this->assertSame('F', $result['grade_letter']);
    }

    // ---- No grade found: mark below the lowest range ----

    public function test_calculate_negative_throws_grade_not_found(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculate(-1);
    }

    // ---- No grade found: mark above the highest range ----

    public function test_calculate_above_max_throws_grade_not_found(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculate(101);
    }

    // ---- No grade found: gap between two grades ----

    public function test_calculate_gap_throws_grade_not_found(): void
    {
        $grades = [
            (function (): Grade {
                $g = new Grade();
                $g->setRawAttributes(['id' => 1, 'grade_name' => 'High', 'grade_letter' => 'H', 'min_mark' => 50, 'max_mark' => 100, 'gpa_point' => 3.0]);
                return $g;
            })(),
            (function (): Grade {
                $g = new Grade();
                $g->setRawAttributes(['id' => 2, 'grade_name' => 'Low', 'grade_letter' => 'L', 'min_mark' => 0, 'max_mark' => 30, 'gpa_point' => 1.0]);
                return $g;
            })(),
        ];

        $this->expectException(GradeNotFoundException::class);

        $this->createService($grades)->calculate(40);
    }

    // ---- Invalid configuration: empty grades collection ----

    public function test_calculate_empty_grades_throws_grade_not_found(): void
    {
        $repo = $this->createMock(GradeRepositoryInterface::class);
        $repo->method('allOrdered')->willReturn(new Collection());

        $this->expectException(GradeNotFoundException::class);

        (new GradeCalculationService($repo))->calculate(75);
    }

    // ---- Invalid configuration: grades do not cover full range ----

    public function test_calculate_grades_not_covering_zero_throws_exception(): void
    {
        $grades = [
            (function (): Grade {
                $g = new Grade();
                $g->setRawAttributes(['id' => 1, 'grade_name' => 'Pass', 'grade_letter' => 'P', 'min_mark' => 50, 'max_mark' => 100, 'gpa_point' => 2.0]);
                return $g;
            })(),
        ];

        $this->expectException(GradeNotFoundException::class);

        $this->createService($grades)->calculate(25);
    }

    // ---- Result structure ----

    public function test_calculate_returns_complete_structure(): void
    {
        $result = $this->createService($this->defaultGrades())->calculate(85);

        $this->assertArrayHasKey('grade_id', $result);
        $this->assertArrayHasKey('grade_name', $result);
        $this->assertArrayHasKey('grade_letter', $result);
        $this->assertArrayHasKey('gpa', $result);
        $this->assertIsInt($result['grade_id']);
        $this->assertIsString($result['grade_name']);
        $this->assertIsString($result['grade_letter']);
        $this->assertIsFloat($result['gpa']);
    }

    // ---- calculateCollection ----

    public function test_calculate_collection_returns_all_results(): void
    {
        $results = $this->createService($this->defaultGrades())->calculateCollection([
            's1' => ['obtained_mark' => 85],
            's2' => ['obtained_mark' => 72],
            's3' => ['obtained_mark' => 55],
        ]);

        $this->assertCount(3, $results);
        $this->assertSame('A+', $results['s1']['grade_letter']);
        $this->assertSame('A-', $results['s2']['grade_letter']);
        $this->assertSame('B-', $results['s3']['grade_letter']);
    }

    public function test_calculate_collection_prefers_total_mark(): void
    {
        $results = $this->createService($this->defaultGrades())->calculateCollection([
            's1' => ['total_mark' => 42, 'obtained_mark' => 90],
        ]);

        $this->assertSame('C-', $results['s1']['grade_letter']);
    }

    public function test_calculate_collection_throws_on_no_match(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->createService($this->defaultGrades())->calculateCollection([
            's1' => ['obtained_mark' => -5],
        ]);
    }

    public function test_calculate_collection_throws_on_empty_grades(): void
    {
        $repo = $this->createMock(GradeRepositoryInterface::class);
        $repo->method('allOrdered')->willReturn(new Collection());

        $this->expectException(GradeNotFoundException::class);

        (new GradeCalculationService($repo))->calculateCollection([
            's1' => ['obtained_mark' => 75],
        ]);
    }

    // ---- GPA values are correctly mapped ----

    public function test_calculate_gpa_values(): void
    {
        $service = $this->createService($this->defaultGrades());

        $this->assertEquals(4.00, $service->calculate(100)['gpa']);
        $this->assertEquals(3.75, $service->calculate(77)['gpa']);
        $this->assertEquals(3.50, $service->calculate(72)['gpa']);
        $this->assertEquals(3.25, $service->calculate(67)['gpa']);
        $this->assertEquals(3.00, $service->calculate(62)['gpa']);
        $this->assertEquals(2.75, $service->calculate(57)['gpa']);
        $this->assertEquals(2.50, $service->calculate(52)['gpa']);
        $this->assertEquals(2.25, $service->calculate(47)['gpa']);
        $this->assertEquals(2.00, $service->calculate(42)['gpa']);
        $this->assertEquals(1.50, $service->calculate(37)['gpa']);
        $this->assertEquals(0.00, $service->calculate(10)['gpa']);
    }

    // ---- Repositories are cached after first call ----

    public function test_repository_is_only_called_once(): void
    {
        $repo = $this->createMock(GradeRepositoryInterface::class);
        $repo->expects($this->once())->method('allOrdered')
            ->willReturn(new Collection($this->defaultGrades()));

        $service = new GradeCalculationService($repo);

        $service->calculate(85);
        $service->calculate(72);
        $service->calculate(55);
    }
}
