<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Morning', 'start_time' => '07:00', 'end_time' => '12:00', 'description' => 'Morning shift (7 AM - 12 PM)'],
            ['name' => 'Afternoon', 'start_time' => '12:00', 'end_time' => '16:00', 'description' => 'Afternoon shift (12 PM - 4 PM)'],
            ['name' => 'Evening', 'start_time' => '16:00', 'end_time' => '20:00', 'description' => 'Evening shift (4 PM - 8 PM)'],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(['name' => $shift['name']], $shift);
        }
    }
}
