<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\AttendanceBulkUpdateRequest;
use App\Http\Requests\Attendance\AttendanceFilterRequest;
use App\Http\Requests\Attendance\AttendanceHistoryRequest;
use App\Http\Requests\Attendance\AttendanceUpdateRequest;
use App\Http\Resources\AttendanceRecordResource;
use App\Http\Resources\AttendanceSessionResource;
use App\Http\Responses\ApiResponse;
use App\Services\AttendanceBulkService;
use App\Services\AttendanceRecordService;
use App\Services\AttendanceSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AttendanceSessionService $sessionService,
        private readonly AttendanceRecordService $recordService,
        private readonly AttendanceBulkService $bulkService,
    ) {}

    public function index(): View
    {
        return view('attendance.index');
    }

    public function loadStudents(AttendanceFilterRequest $request): JsonResponse
    {
        $result = $this->sessionService->loadStudents(
            $request->validated(),
            (int) $request->user()->id,
        );

        return $this->success(
            __('attendance.students_loaded'),
            [
                'session' => new AttendanceSessionResource($result['session']),
                'records' => AttendanceRecordResource::collection($result['records']),
                'summary' => $result['summary'],
            ],
        );
    }

    public function update(AttendanceUpdateRequest $request): JsonResponse
    {
        $result = $this->recordService->update($request->validated());

        return $this->success(
            __('attendance.attendance_updated'),
            [
                'attendance' => [
                    'status' => $result['record']->attendance_status,
                    'checked_at' => $result['record']->checked_at?->toISOString(),
                ],
                'summary' => $result['summary'],
            ],
        );
    }

    public function bulkUpdate(AttendanceBulkUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $summary = $this->bulkService->bulkUpdate(
            (int) $data['attendance_session_id'],
            $data['student_ids'],
            $data['attendance_status'],
        );

        return $this->success(
            __('attendance.bulk_updated'),
            ['summary' => $summary],
        );
    }

    public function session(int $id): JsonResponse
    {
        $session = $this->sessionService->findByIdWithRelations($id);

        if (!$session) {
            return $this->notFound(__('attendance.session_not_found'));
        }

        return $this->success(
            __('attendance.session_retrieved'),
            new AttendanceSessionResource($session),
        );
    }

    public function history(AttendanceHistoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $perPage = (int) ($data['per_page'] ?? 15);
        unset($data['per_page']);

        $sessions = $this->sessionService->paginateHistory($data, $perPage);

        return $this->success(
            __('attendance.history_retrieved'),
            AttendanceSessionResource::collection($sessions),
            [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'per_page' => $sessions->perPage(),
                'total' => $sessions->total(),
            ],
        );
    }

    public function search(Request $request): View
    {
        return $this->index();
    }

    public function export(): JsonResponse
    {
        return $this->success('Export functionality coming soon.');
    }

    public function print(): View
    {
        return $this->index();
    }
}
