<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\StoreExamRequest;
use App\Http\Requests\Examination\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Http\Responses\ApiResponse;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Semester;
use App\Services\AcademicYearService;
use App\Services\DepartmentService;
use App\Services\ExamService;
use App\Services\ProgramService;
use App\Services\SectionService;
use App\Services\ShiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ExamService $service,
        private readonly AcademicYearService $academicYearService,
        private readonly DepartmentService $departmentService,
        private readonly ProgramService $programService,
        private readonly ShiftService $shiftService,
        private readonly SectionService $sectionService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only([
            'academic_year_id', 'semester_id', 'department_id',
            'program_id', 'shift_id', 'section_id', 'exam_type', 'search',
        ]);

        $query = Exam::with([
            'examType', 'academicYear', 'semester', 'department',
            'program', 'shift', 'section',
        ])->withCount('examSubjects');

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }
        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }
        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }
        if (!empty($filters['exam_type'])) {
            $query->where('exam_type_id', $filters['exam_type']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%");
        }

        $exams = $query->orderBy('start_date', 'desc')->paginate(15)->appends($filters);

        $academicYears = $this->academicYearService->all();
        $semesters = Semester::query()->get();
        $departments = $this->departmentService->all();
        $programs = $this->programService->all();
        $shifts = $this->shiftService->all();
        $sections = $this->sectionService->all();
        $examTypes = ExamType::query()->get();

        $stats = [
            'total' => Exam::count(),
            'upcoming' => Exam::where('start_date', '>', now()->toDateString())->count(),
            'ongoing' => Exam::where('start_date', '<=', now()->toDateString())
                ->where('end_date', '>=', now()->toDateString())->count(),
            'completed' => Exam::where('end_date', '<', now()->toDateString())->count(),
        ];

        return view('examinations.index', compact(
            'exams', 'filters', 'academicYears', 'semesters', 'departments',
            'programs', 'shifts', 'sections', 'examTypes', 'stats',
        ));
    }

    public function store(StoreExamRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = (int) $request->user()->id;
        $exam = $this->service->create($data);

        return $this->created(
            __('examination.exam_created'),
            new ExamResource($exam),
        );
    }

    public function show(int $id): JsonResponse
    {
        $exam = $this->service->findWithRelations($id);

        if (!$exam) {
            return $this->notFound(__('examination.exam_not_found'));
        }

        return $this->success(
            __('examination.exam_retrieved'),
            new ExamResource($exam),
        );
    }

    public function update(UpdateExamRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = (int) $request->user()->id;
        $exam = $this->service->update($id, $data);

        return $this->success(
            __('examination.exam_updated'),
            new ExamResource($exam),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(__('examination.exam_deleted'));
    }

    public function create(): View
    {
        return view('examinations.index');
    }

    public function edit(int $id): View
    {
        return view('examinations.index');
    }

    public function search(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $exams = $this->service->paginate($perPage);

        return $this->success(
            __('examination.exams_retrieved'),
            ExamResource::collection($exams),
            [
                'current_page' => $exams->currentPage(),
                'last_page' => $exams->lastPage(),
                'per_page' => $exams->perPage(),
                'total' => $exams->total(),
            ],
        );
    }

    public function export(): JsonResponse
    {
        return $this->success('Export functionality coming soon.');
    }

    public function print(): JsonResponse
    {
        return $this->success('Print functionality coming soon.');
    }
}
