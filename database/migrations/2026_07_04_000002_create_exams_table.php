<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('exam_type_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('academic_year_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('semester_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('department_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('program_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('shift_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('section_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('title', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('draft');

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

            $table->index('academic_year_id');
            $table->index('semester_id');
            $table->index('department_id');
            $table->index('section_id');
            $table->index('shift_id');
            $table->index('program_id');
            $table->index('exam_type_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');

            $table->unique(
                ['exam_type_id', 'academic_year_id', 'semester_id', 'department_id', 'program_id', 'shift_id', 'section_id', 'start_date'],
                'exams_type_year_sem_dept_prog_shift_section_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
