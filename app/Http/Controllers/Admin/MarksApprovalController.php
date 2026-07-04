<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\MarksApprovalRequest;
use App\Http\Resources\MarkResource;
use App\Http\Responses\ApiResponse;
use App\Services\MarksApprovalService;
use Illuminate\Http\JsonResponse;

class MarksApprovalController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MarksApprovalService $service,
    ) {}

    public function pending(): JsonResponse
    {
        $marks = $this->service->pendingList();

        return $this->success(
            __('examination.pending_retrieved'),
            MarkResource::collection($marks),
        );
    }

    public function approve(MarksApprovalRequest $request, int $id): JsonResponse
    {
        $mark = $this->service->approve($id, (int) $request->user()->id);

        return $this->success(
            __('examination.approval_approved'),
            new MarkResource($mark),
        );
    }

    public function reject(MarksApprovalRequest $request, int $id): JsonResponse
    {
        $mark = $this->service->reject(
            $id,
            (int) $request->user()->id,
            $request->input('remark'),
        );

        return $this->success(
            __('examination.approval_rejected'),
            new MarkResource($mark),
        );
    }

    public function reset(int $id): JsonResponse
    {
        $mark = $this->service->reset($id);

        return $this->success(
            __('examination.approval_reset'),
            new MarkResource($mark),
        );
    }
}
