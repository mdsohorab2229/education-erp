<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\MarksEntryRequest;
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
        $marks = $this->service->getExamMarks($examId);

        return $this->success(
            __('examination.marks_retrieved'),
            MarkResource::collection($marks),
        );
    }

    public function loadStudents(Request $request): JsonResponse
    {
        $examSubjectId = (int) $request->query('exam_subject_id');
        $result = $this->service->loadStudents($examSubjectId);

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

        $rows = array_map(
            fn (array $mark): array => array_merge($mark, ['exam_subject_id' => $data['exam_subject_id']]),
            $data['marks'],
        );

        $result = $this->service->bulkStore($rows);

        return $this->created(
            __('examination.marks_stored'),
            $result,
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $mark = $this->service->updateMark(
                $id,
                $request->only(['obtained_mark', 'practical_mark', 'viva_mark', 'remark']),
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 404);
        }

        return $this->success(
            __('examination.mark_updated'),
            new MarkResource($mark),
        );
    }
}
