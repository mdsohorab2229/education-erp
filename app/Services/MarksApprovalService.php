<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InvalidApprovalStateException;
use App\Interfaces\Repositories\MarkRepositoryInterface;
use App\Models\Mark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarksApprovalService
{
    private const string STATUS_PENDING = 'pending';

    private const string STATUS_APPROVED = 'approved';

    private const string STATUS_REJECTED = 'rejected';

    public function __construct(
        private readonly MarkRepositoryInterface $markRepository,
    ) {}

    public function approve(int $markId, int $userId): Mark
    {
        return DB::transaction(function () use ($markId, $userId): Mark {
            $mark = $this->findMarkOrFail($markId);
            $this->assertStatusIs($mark, self::STATUS_PENDING);

            return $this->markRepository->update($markId, [
                'approval_status' => self::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);
        });
    }

    public function reject(int $markId, int $userId, string $remark): Mark
    {
        return DB::transaction(function () use ($markId, $userId, $remark): Mark {
            $mark = $this->findMarkOrFail($markId);
            $this->assertStatusIs($mark, self::STATUS_PENDING);

            return $this->markRepository->update($markId, [
                'approval_status' => self::STATUS_REJECTED,
                'approved_by' => $userId,
                'approved_at' => now(),
                'remark' => $remark,
            ]);
        });
    }

    public function reset(int $markId): Mark
    {
        return DB::transaction(function () use ($markId): Mark {
            $mark = $this->findMarkOrFail($markId);
            $this->assertStatusIsNot($mark, self::STATUS_PENDING);

            return $this->markRepository->update($markId, [
                'approval_status' => self::STATUS_PENDING,
                'approved_by' => null,
                'approved_at' => null,
                'remark' => null,
            ]);
        });
    }

    public function pendingList(): Collection
    {
        return $this->markRepository->pendingApproval();
    }

    public function pendingListPaginated(int $perPage = 50): LengthAwarePaginator
    {
        return $this->markRepository->pendingApprovalPaginated($perPage);
    }

    private function findMarkOrFail(int $markId): Mark
    {
        $mark = $this->markRepository->withRelations($markId);

        if (!$mark) {
            throw new \RuntimeException(__('examination.mark_not_found'));
        }

        return $mark;
    }

    private function assertStatusIs(Mark $mark, string $status): void
    {
        if ($mark->approval_status !== $status) {
            throw new InvalidApprovalStateException(
                __('examination.approval_invalid_state', ['status' => $mark->approval_status])
            );
        }
    }

    private function assertStatusIsNot(Mark $mark, string $status): void
    {
        if ($mark->approval_status === $status) {
            throw new InvalidApprovalStateException(
                __('examination.approval_invalid_state', ['status' => $mark->approval_status])
            );
        }
    }
}
