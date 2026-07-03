<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routines', function (Blueprint $table): void {
            $table->id();

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
                ->nullable()
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('shift_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('section_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('subject_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('room_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('day_of_week', 10);
            $table->time('start_time');
            $table->time('end_time');
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

            $table->unique(['room_id', 'day_of_week', 'start_time'], 'routine_room_slot_unique');
            $table->unique(['teacher_id', 'day_of_week', 'start_time'], 'routine_teacher_slot_unique');

            $table->index('status');
            $table->index('day_of_week');
            $table->index('subject_id');
            $table->index('academic_year_id');
            $table->index('semester_id');

            $table->index(['section_id', 'day_of_week'], 'routine_section_weekly_idx');
            $table->index(['department_id', 'day_of_week'], 'routine_department_weekly_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
