<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentAdmissionRequest;
use App\Http\Requests\Admin\StudentStatusRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Student;
use App\Services\AcademicYearService;
use App\Services\GroupService;
use App\Services\ProgramService;
use App\Services\SectionService;
use App\Services\ShiftService;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService,
        private readonly AcademicYearService $academicYearService,
        private readonly ProgramService $programService,
        private readonly SectionService $sectionService,
        private readonly ShiftService $shiftService,
        private readonly GroupService $groupService,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Student::class);

        $filters = $request->only(['status', 'program_id', 'section_id', 'academic_year_id', 'shift_id', 'search']);
        $students = $this->studentService->paginateWithFilters($filters, 15);

        $academicYears = $this->academicYearService->all();
        $programs = $this->programService->all();
        $sections = $this->sectionService->all();
        $shifts = $this->shiftService->all();

        $stats = [
            'total' => $students->total(),
            'active' => $this->studentService->countByStatus('active'),
            'inactive' => $this->studentService->countByStatus('inactive'),
        ];

        return view('admin.students.index', compact(
            'students', 'filters', 'academicYears', 'programs', 'sections', 'shifts', 'stats'
        ));
    }

    public function create(): View
    {
        $this->authorize('create', Student::class);

        $academicYears = $this->academicYearService->all();
        $programs = $this->programService->all();
        $sections = $this->sectionService->all();
        $shifts = $this->shiftService->all();
        $groups = $this->groupService->all();

        return view('admin.students.create', compact(
            'academicYears', 'programs', 'sections', 'shifts', 'groups'
        ));
    }

    public function store(StudentAdmissionRequest $request): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $this->studentService->admit($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student admitted successfully.');
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $student = $this->studentService->findById($student->id);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $this->authorize('update', $student);

        $student = $this->studentService->findById($student->id);
        $academicYears = $this->academicYearService->all();
        $programs = $this->programService->all();
        $sections = $this->sectionService->all();
        $shifts = $this->shiftService->all();
        $groups = $this->groupService->all();

        return view('admin.students.edit', compact(
            'student', 'academicYears', 'programs', 'sections', 'shifts', 'groups'
        ));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $this->studentService->update($student->id, $data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);

        $this->studentService->delete($student->id);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function status(StudentStatusRequest $request, Student $student): RedirectResponse
    {
        $this->studentService->changeStatus($student->id, $request->input('status'));

        return redirect()->route('admin.students.index')
            ->with('success', 'Student status updated successfully.');
    }
}
