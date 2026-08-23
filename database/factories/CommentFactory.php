<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => null,
            'parent_id' => null,
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'body' => fake()->paragraph(),
            'status' => 'approved',
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function spam(): static
    {
        return $this->state(fn () => ['status' => 'spam']);
    }

    public function reply(Comment $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
            'post_id' => $parent->post_id,
        ]);
    }
}
