<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignDepartmentsRequest;
use App\Http\Requests\Admin\AssignSubjectsRequest;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\TeacherQualification;
use App\Services\DepartmentService;
use App\Services\SubjectService;
use App\Services\TeacherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function __construct(
        private readonly TeacherService $teacherService,
        private readonly SubjectService $subjectService,
        private readonly DepartmentService $departmentService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Teacher::class);

        $filters = $request->only(['status', 'department_id', 'search']);
        $teachers = $this->teacherService->paginateWithFilters($filters, 15);

        $departments = $this->departmentService->all();

        $stats = [
            'total' => $teachers->total(),
            'active' => $this->teacherService->countByStatus('active'),
            'inactive' => $this->teacherService->countByStatus('inactive'),
        ];

        return view('admin.teachers.index', compact('teachers', 'filters', 'departments', 'stats'));
    }

    public function create(): View
    {
        $this->authorize('create', Teacher::class);

        $departments = $this->departmentService->all();
        $subjects = $this->subjectService->all();

        return view('admin.teachers.create', compact('departments', 'subjects'));
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $this->authorize('create', Teacher::class);

        $data = $request->validated();
        $data['employee_id'] = $this->teacherService->generateEmployeeId();

        $qualifications = $data['qualifications'] ?? [];
        unset($data['qualifications']);

        $teacher = $this->teacherService->create($data);

        foreach ($qualifications as $qualData) {
            $teacher->qualifications()->create($qualData);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $this->authorize('view', $teacher);

        $teacher = $this->teacherService->findById($teacher->id);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        $this->authorize('update', $teacher);

        $teacher = $this->teacherService->findById($teacher->id);
        $departments = $this->departmentService->all();
        $subjects = $this->subjectService->all();

        return view('admin.teachers.edit', compact('teacher', 'departments', 'subjects'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $this->authorize('update', $teacher);

        $data = $request->validated();
        $qualifications = $data['qualifications'] ?? [];
        unset($data['qualifications']);

        $this->teacherService->update($teacher->id, $data);

        $existingIds = [];
        foreach ($qualifications as $qualData) {
            if (!empty($qualData['id'])) {
                TeacherQualification::where('id', $qualData['id'])
                    ->where('teacher_id', $teacher->id)
                    ->update([
                        'degree' => $qualData['degree'],
                        'institution' => $qualData['institution'],
                        'year' => $qualData['year'] ?? null,
                        'grade' => $qualData['grade'] ?? null,
                    ]);
                $existingIds[] = $qualData['id'];
            } else {
                $qual = $teacher->qualifications()->create($qualData);
                $existingIds[] = $qual->id;
            }
        }

        $teacher->qualifications()
            ->whereNotIn('id', $existingIds)
            ->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $this->authorize('delete', $teacher);

        $this->teacherService->delete($teacher->id);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    public function assignSubjects(AssignSubjectsRequest $request, Teacher $teacher): RedirectResponse
    {
        $this->authorize('update', $teacher);

        try {
            $this->teacherService->syncSubjects($teacher->id, $request->input('subject_ids'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.teachers.show', $teacher->id)
            ->with('success', 'Subjects assigned successfully.');
    }

    public function assignDepartments(AssignDepartmentsRequest $request, Teacher $teacher): RedirectResponse
    {
        $this->authorize('update', $teacher);

        try {
            $this->teacherService->syncDepartments($teacher->id, $request->input('department_ids'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.teachers.show', $teacher->id)
            ->with('success', 'Departments assigned successfully.');
    }

    public function search(Request $request): View
    {
        return $this->index($request);
    }

    public function export(): RedirectResponse
    {
        return redirect()->route('admin.teachers.index')->with('info', 'Export functionality coming soon.');
    }

    public function print(Request $request): View
    {
        return $this->index($request);
    }
}
