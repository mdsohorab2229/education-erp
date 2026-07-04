<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\MarksApprovalRequest;
use App\Http\Resources\MarkResource;
use App\Http\Responses\ApiResponse;
use App\Services\MarksApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarksApprovalController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly MarksApprovalService $service,
    ) {}

    public function pending(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 50);
        $marks = $this->service->pendingListPaginated($perPage);

        return $this->success(
            __('examination.pending_retrieved'),
            MarkResource::collection($marks),
            [
                'current_page' => $marks->currentPage(),
                'last_page' => $marks->lastPage(),
                'per_page' => $marks->perPage(),
                'total' => $marks->total(),
            ],
        );
    }

    public function approve(MarksApprovalRequest $request, int $id): JsonResponse
    {
        try {
            $mark = $this->service->approve($id, (int) $request->user()->id);
        } catch (\App\Exceptions\InvalidApprovalStateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->success(
            __('examination.approval_approved'),
            new MarkResource($mark),
        );
    }

    public function reject(MarksApprovalRequest $request, int $id): JsonResponse
    {
        try {
            $mark = $this->service->reject(
                $id,
                (int) $request->user()->id,
                $request->input('remark'),
            );
        } catch (\App\Exceptions\InvalidApprovalStateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->success(
            __('examination.approval_rejected'),
            new MarkResource($mark),
        );
    }

    public function reset(int $id): JsonResponse
    {
        try {
            $mark = $this->service->reset($id);
        } catch (\App\Exceptions\InvalidApprovalStateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\RuntimeException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->success(
            __('examination.approval_reset'),
            new MarkResource($mark),
        );
    }

    public function search(Request $request): JsonResponse
    {
        return $this->pending($request);
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
