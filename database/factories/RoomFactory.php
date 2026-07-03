<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Room 101', 'Room 102', 'Room 103', 'Lab 1', 'Lab 2', 'Lecture Hall']),
            'code' => fake()->unique()->regexify('[A-Z]{2,3}-[0-9]{3}'),
            'capacity' => fake()->numberBetween(20, 100),
            'type' => fake()->randomElement(['classroom', 'lab', 'lecture_hall']),
            'status' => 'active',
        ];
    }
}
