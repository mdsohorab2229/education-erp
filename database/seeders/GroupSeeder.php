<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Program;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::all();

        foreach ($programs as $program) {
            $letters = ['A', 'B'];

            foreach ($letters as $letter) {
                Group::firstOrCreate(
                    ['program_id' => $program->id, 'name' => "Group {$letter}"],
                    ['capacity' => 30, 'description' => "Group {$letter} for {$program->name}"]
                );
            }
        }
    }
}
