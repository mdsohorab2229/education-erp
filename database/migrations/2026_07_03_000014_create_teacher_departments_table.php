<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_departments', function (Blueprint $table): void {
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->unique(['teacher_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_departments');
    }
};
