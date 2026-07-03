<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('assignment_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('student_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('submission_file', 255);
            $table->dateTime('submitted_at');
            $table->decimal('marks', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->string('status', 20)->default('submitted');

            $table->timestamps();

            $table->unique(['assignment_id', 'student_id'], 'submissions_assignment_student_unique');

            $table->index('status');
            $table->index('student_id');
            $table->index('assignment_id');
            $table->index('submitted_at');

            $table->index(['assignment_id', 'status'], 'submissions_assignment_status_idx');
            $table->index(['student_id', 'status'], 'submissions_student_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
