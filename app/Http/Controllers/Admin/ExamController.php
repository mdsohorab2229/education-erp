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
use Illuminate\View\View;

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
        return view('admin.exams.index');
    }

    public function edit(int $id): View
    {
        return view('admin.exams.index');
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
