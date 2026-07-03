<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignmentStoreRequest;
use App\Http\Requests\Admin\AssignmentSubmissionRequest;
use App\Http\Requests\Admin\AssignmentUpdateRequest;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\AssignmentSubmissionResource;
use App\Models\Student;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly AssignmentService $service,
    ) {}

    public function indexView(): View
    {
        return view('admin.assignments.index');
    }

    public function createView(): View
    {
        return view('admin.assignments.create');
    }

    public function submitView(): View
    {
        return view('admin.assignments.submit');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        return AssignmentResource::collection($this->service->paginate($perPage));
    }

    public function store(AssignmentStoreRequest $request): AssignmentResource
    {
        $assignment = $this->service->create($request->validated());

        return new AssignmentResource($assignment);
    }

    public function show(int $id): AssignmentResource
    {
        $assignment = $this->service->findById($id);

        if (!$assignment) {
            abort(404, 'Assignment not found.');
        }

        return new AssignmentResource($assignment);
    }

    public function update(AssignmentUpdateRequest $request, int $id): AssignmentResource
    {
        $assignment = $this->service->update($id, $request->validated());

        return new AssignmentResource($assignment);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(['message' => 'Assignment deleted successfully.']);
    }

    public function submit(AssignmentSubmissionRequest $request): AssignmentSubmissionResource
    {
        $studentId = $request->input('student_id')
            ?? Student::where('email', auth()->user()->email)?->first()?->id;

        if (!$studentId) {
            abort(422, 'Student not found. Please provide a valid student ID.');
        }

        $submission = $this->service->submit(
            (int) $request->input('assignment_id'),
            (int) $studentId,
            $request->file('submission_file'),
        );

        return new AssignmentSubmissionResource($submission);
    }

    public function marks(Request $request, int $submissionId): AssignmentSubmissionResource
    {
        $submission = $this->service->updateMarks(
            $submissionId,
            $request->input('marks'),
            $request->input('feedback'),
        );

        return new AssignmentSubmissionResource($submission);
    }

    public function bySection(Request $request): AnonymousResourceCollection
    {
        $assignments = $this->service->getBySection(
            (int) $request->query('section_id'),
            $request->query('status'),
        );

        return AssignmentResource::collection($assignments);
    }

    public function byTeacher(Request $request): AnonymousResourceCollection
    {
        $assignments = $this->service->getByTeacher(
            (int) $request->query('teacher_id'),
            $request->query('status'),
        );

        return AssignmentResource::collection($assignments);
    }

    public function upcoming(Request $request): AnonymousResourceCollection
    {
        $assignments = $this->service->getUpcoming(
            (int) $request->query('section_id'),
        );

        return AssignmentResource::collection($assignments);
    }
}
