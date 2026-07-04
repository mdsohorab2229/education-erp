<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\MarksEntryRequest;
use App\Http\Requests\Examination\UpdateMarkRequest;
use App\Http\Resources\ExamSubjectResource;
use App\Http\Resources\MarkResource;
use App\Http\Responses\ApiResponse;
use App\Services\MarksEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarksEntryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MarksEntryService $service,
    ) {}

    public function index(Request $request): JsonResponse
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

    public function bulkStore(MarksEntryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = (int) $request->user()->id;

        $marks = $this->service->bulkStore($data);

        return $this->created(
            __('examination.marks_stored'),
            ['processed' => count($marks)],
        );
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
        return $this->index($request);
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
