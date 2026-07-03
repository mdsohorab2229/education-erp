<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('attendance_session_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('student_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('attendance_status', 2)->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('checked_at')->nullable();

            $table->timestamps();

            $table->unique(['attendance_session_id', 'student_id'], 'attendance_record_unique');

            $table->index('student_id');
            $table->index('attendance_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
