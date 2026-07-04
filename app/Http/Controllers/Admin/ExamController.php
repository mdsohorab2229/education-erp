<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\StoreExamRequest;
use App\Http\Requests\Examination\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Http\Responses\ApiResponse;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ExamService $service,
    ) {}

    public function index(Request $request): JsonResponse
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

    public function store(StoreExamRequest $request): JsonResponse
    {
        $exam = $this->service->create($request->validated());

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
        $exam = $this->service->update($id, $request->validated());

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
}
