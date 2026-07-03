<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::all();

        foreach ($programs as $program) {
            foreach (['Section A', 'Section B'] as $name) {
                Section::firstOrCreate(
                    ['program_id' => $program->id, 'name' => $name],
                    ['capacity' => 50]
                );
            }
        }
    }
}
