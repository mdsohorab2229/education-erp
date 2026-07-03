<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AttendanceRecordService
{
    public function __construct(
        private readonly AttendanceRecordRepositoryInterface $recordRepository,
        private readonly AttendanceSummaryService $summaryService,
    ) {}

    public function update(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $record = $this->recordRepository->upsert($data);
            $summary = $this->summaryService->recalculate((int) $data['attendance_session_id']);

            return [
                'record' => $record,
                'summary' => $summary,
            ];
        });
    }
}
