<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentCommentRequest;
use App\Http\Requests\Admin\ContentUpdateRequest;
use App\Http\Requests\Admin\ContentUploadRequest;
use App\Http\Resources\ContentCommentResource;
use App\Http\Resources\ContentResource;
use App\Services\ContentUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(
        private readonly ContentUploadService $service,
    ) {}

    public function listView(): View
    {
        return view('admin.content.index');
    }

    public function uploadView(): View
    {
        return view('admin.content.upload');
    }

    public function showView(int $id): View
    {
        return view('admin.content.show', ['id' => $id]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        return ContentResource::collection($this->service->paginate($perPage));
    }

    public function store(ContentUploadRequest $request): ContentResource
    {
        $content = $this->service->upload(
            $request->safe()->except('file'),
            $request->file('file'),
        );

        return new ContentResource($content);
    }

    public function show(int $id): ContentResource
    {
        $content = $this->service->findById($id);

        if (!$content) {
            abort(404, 'Content not found.');
        }

        return new ContentResource($content);
    }

    public function update(ContentUpdateRequest $request, int $id): ContentResource
    {
        $content = $this->service->update($id, $request->safe()->except('file'));

        return new ContentResource($content);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(['message' => 'Content deleted successfully.']);
    }

    public function bySection(Request $request): AnonymousResourceCollection
    {
        $contents = $this->service->getBySection(
            (int) $request->query('section_id'),
            $request->query('type'),
        );

        return ContentResource::collection($contents);
    }

    public function byTeacher(Request $request): AnonymousResourceCollection
    {
        $contents = $this->service->getByTeacher(
            (int) $request->query('teacher_id'),
            $request->query('type'),
        );

        return ContentResource::collection($contents);
    }

    public function download(int $id): mixed
    {
        $content = $this->service->findById($id);

        if (!$content) {
            abort(404, 'Content not found.');
        }

        if (!Storage::disk('public')->exists($content->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($content->file_path, $content->file_name);
    }

    public function comments(int $id): AnonymousResourceCollection
    {
        $content = $this->service->findById($id);

        if (!$content) {
            abort(404, 'Content not found.');
        }

        $content->load('comments.user');

        return ContentCommentResource::collection($content->comments);
    }

    public function addComment(ContentCommentRequest $request, int $id): ContentCommentResource
    {
        $comment = $this->service->addComment(
            $id,
            auth()->id(),
            $request->input('comment'),
        );

        return new ContentCommentResource($comment);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        return $this->index($request);
    }

    public function export(): JsonResponse
    {
        return response()->json(['message' => 'Export functionality coming soon.']);
    }

    public function print(): JsonResponse
    {
        return response()->json(['message' => 'Print functionality coming soon.']);
    }
}
