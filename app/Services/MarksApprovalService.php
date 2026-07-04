<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InvalidApprovalStateException;
use App\Interfaces\Repositories\MarkRepositoryInterface;
use App\Models\Mark;
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
            $mark = $this->markRepository->withRelations($markId);

            if (!$mark) {
                throw new \RuntimeException("Mark with ID {$markId} not found.");
            }

            if ($mark->approval_status !== self::STATUS_PENDING) {
                throw new InvalidApprovalStateException(
                    "Cannot approve mark with status '{$mark->approval_status}'. Only pending marks can be approved."
                );
            }

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
            $mark = $this->markRepository->withRelations($markId);

            if (!$mark) {
                throw new \RuntimeException("Mark with ID {$markId} not found.");
            }

            if ($mark->approval_status !== self::STATUS_PENDING) {
                throw new InvalidApprovalStateException(
                    "Cannot reject mark with status '{$mark->approval_status}'. Only pending marks can be rejected."
                );
            }

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
            $mark = $this->markRepository->withRelations($markId);

            if (!$mark) {
                throw new \RuntimeException("Mark with ID {$markId} not found.");
            }

            if ($mark->approval_status === self::STATUS_PENDING) {
                throw new InvalidApprovalStateException(
                    "Cannot reset mark with status 'pending'. Only approved or rejected marks can be reset."
                );
            }

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
}
