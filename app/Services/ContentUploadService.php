<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ContentRepositoryInterface;
use App\Jobs\ProcessUploadedContentJob;
use App\Models\Content;
use App\Models\ContentComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentUploadService
{
    private const string DISK = 'public';

    private const string UPLOAD_PATH = 'contents';

    public function __construct(
        private readonly ContentRepositoryInterface $repository,
    ) {}

    public function upload(array $data, UploadedFile $file): Content
    {
        $data['file_name'] = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $data['file_size'] = $file->getSize();
        $data['mime_type'] = $file->getMimeType();
        $data['file_path'] = $file->store(self::UPLOAD_PATH, self::DISK);

        $content = DB::transaction(function () use ($data): Content {
            return $this->repository->create($data);
        });

        ProcessUploadedContentJob::dispatch($content);

        return $content;
    }

    public function delete(int $id): void
    {
        $content = $this->repository->findById($id);

        if (!$content) {
            throw new \RuntimeException("Content with ID {$id} not found.");
        }

        DB::transaction(function () use ($content): void {
            $this->repository->delete($content->id);
        });

        if ($content->file_path) {
            Storage::disk(self::DISK)->delete($content->file_path);
        }
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function update(int $id, array $data): Content
    {
        return DB::transaction(function () use ($id, $data): Content {
            return $this->repository->update($id, $data);
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findById(int $id): ?Content
    {
        return $this->repository->findById($id);
    }

    public function getBySection(int $sectionId, ?string $type = null): Collection
    {
        return $this->repository->getBySection($sectionId, $type);
    }

    public function getByTeacher(int $teacherId, ?string $type = null): Collection
    {
        return $this->repository->getByTeacher($teacherId, $type);
    }

    public function addComment(int $contentId, int $userId, string $comment): ContentComment
    {
        $content = $this->findById($contentId);

        if (!$content) {
            throw new \RuntimeException("Content with ID {$contentId} not found.");
        }

        return DB::transaction(function () use ($content, $userId, $comment): ContentComment {
            $commentModel = $content->comments()->create([
                'user_id' => $userId,
                'comment' => $comment,
            ]);

            $commentModel->load('user');

            return $commentModel;
        });
    }
}
