<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_comments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('content_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->text('comment');

            $table->timestamps();

            $table->index('content_id');
            $table->index('user_id');

            $table->index(['content_id', 'user_id'], 'content_comments_content_user_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_comments');
    }
};
