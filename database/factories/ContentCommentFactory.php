<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Content;
use App\Models\ContentComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentComment>
 */
class ContentCommentFactory extends Factory
{
    protected $model = ContentComment::class;

    public function definition(): array
    {
        return [
            'content_id' => Content::factory(),
            'user_id' => User::factory(),
            'comment' => fake()->paragraph(),
        ];
    }
}
