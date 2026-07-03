<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Content;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessUploadedContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    public int $maxExceptions = 1;

    public function __construct(
        public readonly Content $content
    ) {}

    public function handle(): void
    {
        $this->content->update(['status' => 'processing']);

        try {
            match ($this->content->type) {
                'video' => $this->processVideo(),
                'pdf' => $this->processPdf(),
                default => null,
            };

            $this->content->update(['status' => 'active']);
        } catch (\Throwable $e) {
            $this->content->update(['status' => 'failed']);

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('ProcessUploadedContentJob failed after retries', [
            'content_id' => $this->content->id,
            'error' => $e->getMessage(),
        ]);
    }

    private function processVideo(): void
    {
        if (!class_exists(\FFMpeg\FFMpeg::class)) {
            logger()->warning('FFMpeg not available, skipping video thumbnail generation', [
                'content_id' => $this->content->id,
            ]);
            return;
        }

        $path = Storage::disk('public')->path($this->content->file_path);

        if (!file_exists($path)) {
            return;
        }

        $thumbnailDir = 'contents/thumbnails';
        $thumbnailPath = "{$thumbnailDir}/{$this->content->id}.jpg";

        Storage::disk('public')->makeDirectory($thumbnailDir);

        $ffmpeg = \FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($path);
        $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5));
        $frame->save(Storage::disk('public')->path($thumbnailPath));
    }

    private function processPdf(): void
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            logger()->warning('PdfParser not available, skipping PDF metadata extraction', [
                'content_id' => $this->content->id,
            ]);
            return;
        }

        $path = Storage::disk('public')->path($this->content->file_path);

        if (!file_exists($path)) {
            return;
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            $meta = $pdf->getDetails();

            $this->content->update([
                'description' => ($this->content->description ?? '')
                    ?: Str::limit($text, 500),
            ]);
        } catch (\Exception $e) {
            logger()->warning('PDF metadata extraction failed', [
                'content_id' => $this->content->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
