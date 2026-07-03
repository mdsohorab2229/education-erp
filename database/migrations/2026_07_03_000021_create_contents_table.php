<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('subject_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('section_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('title', 255);
            $table->string('type', 20);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->unsigned();
            $table->string('mime_type', 100);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
            $table->index('teacher_id');
            $table->index('subject_id');
            $table->index('section_id');

            $table->index(['section_id', 'type'], 'contents_section_type_idx');
            $table->index(['teacher_id', 'type'], 'contents_teacher_type_idx');
            $table->index(['subject_id', 'section_id'], 'contents_subject_section_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
