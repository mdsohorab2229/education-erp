<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            ['name' => '2023-2024', 'start_date' => '2023-04-01', 'end_date' => '2024-03-31', 'is_current' => false],
            ['name' => '2024-2025', 'start_date' => '2024-04-01', 'end_date' => '2025-03-31', 'is_current' => false],
            ['name' => '2025-2026', 'start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'is_current' => true],
        ];

        foreach ($years as $year) {
            AcademicYear::firstOrCreate(['name' => $year['name']], $year);
        }
    }
}
