<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table): void {
            $table->id();

            $table->string('grade_name', 50)->unique();
            $table->string('grade_letter', 10);
            $table->decimal('min_mark', 5, 2);
            $table->decimal('max_mark', 5, 2);
            $table->decimal('gpa_point', 4, 2);
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('active');

            $table->timestamps();

            $table->index('status');
            $table->index('min_mark');
            $table->index('max_mark');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
