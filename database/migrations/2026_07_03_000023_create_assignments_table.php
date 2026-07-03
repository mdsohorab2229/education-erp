<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table): void {
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
            $table->text('description')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->date('due_date');
            $table->decimal('total_marks', 5, 2);
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
            $table->index('due_date');
            $table->index('teacher_id');
            $table->index('subject_id');
            $table->index('section_id');

            $table->index(['section_id', 'due_date'], 'assignments_section_due_idx');
            $table->index(['teacher_id', 'due_date'], 'assignments_teacher_due_idx');
            $table->index(['subject_id', 'section_id'], 'assignments_subject_section_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
