<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\MarksEntryRequest;
use App\Http\Requests\Examination\UpdateMarkRequest;
use App\Http\Resources\ExamSubjectResource;
use App\Http\Resources\MarkResource;
use App\Http\Responses\ApiResponse;
use App\Models\ExamSubject;
use App\Services\MarksEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarksEntryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MarksEntryService $service,
    ) {}

    public function index(Request $request): View
    {
        $examSubjects = ExamSubject::with(['exam', 'subject', 'teacher'])
            ->orderBy('id')
            ->get();

        $examSubjectId = $request->query('exam_subject_id');
        $examSubject = null;
        $marks = collect();
        $stats = ['total' => 0, 'entered' => 0, 'average' => 0, 'highest' => 0, 'completion' => 0];

        if ($examSubjectId) {
            try {
                $result = $this->service->loadStudents((int) $examSubjectId);
                $examSubject = $result['exam_subject'];
                $marks = $result['marks'];

                $totalStudents = $marks->count();
                $entered = $marks->filter(fn ($m) => $m->obtained_mark !== null || $m->practical_mark !== null || $m->viva_mark !== null)->count();
                $totals = $marks->map(fn ($m) => round((float) ($m->obtained_mark ?? 0) + (float) ($m->practical_mark ?? 0) + (float) ($m->viva_mark ?? 0), 2))->filter(fn ($t) => $t > 0);
                $average = $totals->count() > 0 ? round($totals->sum() / $totals->count(), 1) : 0;
                $highest = $totals->count() > 0 ? $totals->max() : 0;
                $completion = $totalStudents > 0 ? round(($entered / $totalStudents) * 100) : 0;

                $stats = compact('totalStudents', 'entered', 'average', 'highest', 'completion');
                $stats['total'] = $totalStudents;
            } catch (\RuntimeException $e) {
                // exam_subject not found — render empty state
            }
        }

        return view('examinations.marks-entry', compact(
            'examSubjects', 'examSubject', 'marks', 'stats', 'examSubjectId',
        ));
    }

    public function loadStudents(Request $request): JsonResponse
    {
        $examSubjectId = (int) $request->query('exam_subject_id');

        try {
            $result = $this->service->loadStudents($examSubjectId);
        } catch (\RuntimeException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->success(
            __('examination.students_loaded'),
            [
                'exam_subject' => new ExamSubjectResource($result['exam_subject']),
                'marks' => MarkResource::collection($result['marks']),
            ],
        );
    }

    public function bulkStore(MarksEntryRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = (int) $request->user()->id;

        $marks = $this->service->bulkStore($data);

        if ($request->expectsJson()) {
            return $this->created(
                __('examination.marks_stored'),
                ['processed' => count($marks)],
            );
        }

        return redirect()
            ->route('admin.marks.index', ['exam_subject_id' => $data['exam_subject_id'] ?? null])
            ->with('success', __('examination.marks_stored'));
    }

    public function update(UpdateMarkRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = (int) $request->user()->id;
            $mark = $this->service->updateMark($id, $data);
        } catch (\App\Exceptions\GradeNotFoundException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->success(
            __('examination.mark_updated'),
            new MarkResource($mark),
        );
    }

    public function search(Request $request): JsonResponse
    {
        $examId = (int) $request->query('exam_id');
        $perPage = (int) $request->query('per_page', 50);
        $marks = $this->service->getExamMarksPaginated($examId, $perPage);

        return $this->success(
            __('examination.marks_retrieved'),
            MarkResource::collection($marks),
            [
                'current_page' => $marks->currentPage(),
                'last_page' => $marks->lastPage(),
                'per_page' => $marks->perPage(),
                'total' => $marks->total(),
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
