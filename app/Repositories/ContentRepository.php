<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ContentRepositoryInterface;
use App\Models\Content;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContentRepository implements ContentRepositoryInterface
{
    private const array EAGER_LOADS = [
        'teacher',
        'subject',
        'section',
    ];

    public function __construct(
        private readonly Content $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(self::EAGER_LOADS)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Content
    {
        return $this->model->with(self::EAGER_LOADS)->find($id);
    }

    public function create(array $data): Content
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Content
    {
        $content = $this->model->find($id);
        if (!$content) {
            throw new \RuntimeException("Content with ID {$id} not found.");
        }
        $content->update($data);

        return $content;
    }

    public function delete(int $id): bool
    {
        $content = $this->model->find($id);
        if (!$content) {
            throw new \RuntimeException("Content with ID {$id} not found.");
        }

        return (bool) $content->delete();
    }

    public function getBySection(int $sectionId, ?string $type = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('section_id', $sectionId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getByTeacher(int $teacherId, ?string $type = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('teacher_id', $teacherId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getBySubject(int $subjectId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('subject_id', $subjectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
