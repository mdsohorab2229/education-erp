<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\GradeNotFoundException;
use App\Interfaces\Repositories\GradeRepositoryInterface;
use App\Models\Grade;
use App\Services\GradeCalculationService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class GradeCalculationServiceTest extends TestCase
{
    private GradeRepositoryInterface $repository;

    private GradeCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $grades = new Collection([
            (object) ['id' => 1, 'grade_name' => 'A+', 'grade_letter' => 'A+', 'gpa_point' => 4.00, 'min_mark' => 80, 'max_mark' => 100],
            (object) ['id' => 2, 'grade_name' => 'B+', 'grade_letter' => 'B+', 'gpa_point' => 3.25, 'min_mark' => 65, 'max_mark' => 79],
            (object) ['id' => 3, 'grade_name' => 'F', 'grade_letter' => 'F', 'gpa_point' => 0.00, 'min_mark' => 0, 'max_mark' => 34],
        ]);

        $this->repository = Mockery::mock(GradeRepositoryInterface::class);
        $this->repository->shouldReceive('allOrdered')->once()->andReturn($grades);

        $this->service = new GradeCalculationService($this->repository);
    }

    public function test_calculate_returns_correct_grade(): void
    {
        $result = $this->service->calculate(85.00);

        $this->assertEquals(1, $result['grade_id']);
        $this->assertEquals('A+', $result['grade_name']);
        $this->assertEquals('A+', $result['grade_letter']);
        $this->assertEquals(4.00, $result['gpa']);
    }

    public function test_calculate_throws_exception_for_unmatched_mark(): void
    {
        $this->expectException(GradeNotFoundException::class);

        $this->service->calculate(200.00);
    }
}
