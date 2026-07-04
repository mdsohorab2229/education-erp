<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('exam_subject_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('student_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('obtained_mark', 5, 2);
            $table->decimal('practical_mark', 5, 2)->nullable();
            $table->decimal('viva_mark', 5, 2)->nullable();
            $table->decimal('total_mark', 5, 2);

            $table->foreignId('grade_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('approval_status', 20)->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->dateTime('approved_at')->nullable();
            $table->text('remark')->nullable();

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

            $table->timestamps();

            $table->unique(['exam_subject_id', 'student_id'], 'marks_exam_subject_student_unique');

            $table->index('exam_subject_id');
            $table->index('student_id');
            $table->index('grade_id');
            $table->index('approval_status');
            $table->index(['exam_subject_id', 'approval_status'], 'marks_exam_subject_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
