<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $titleBn = fake()->sentence(6);
        $titleEn = fake()->sentence(6);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => ['bn' => $titleBn, 'en' => $titleEn],
            'excerpt' => ['bn' => fake()->sentence(12), 'en' => fake()->sentence(12)],
            'body' => [
                'bn' => '<p>'.fake()->paragraph(5).'</p>',
                'en' => '<p>'.fake()->paragraph(5).'</p>',
            ],
            'type' => 'post',
            'status' => 'published',
            'format' => 'standard',
            'is_premium' => false,
            'is_featured' => false,
            'is_breaking' => false,
            'allow_comments' => true,
            'views' => fake()->numberBetween(0, 5000),
            'published_at' => Carbon::instance(fake()->dateTimeBetween('-30 days', 'now')),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }

    public function premium(): static
    {
        return $this->state(fn () => ['is_premium' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function page(): static
    {
        return $this->state(fn () => ['type' => 'page', 'category_id' => null]);
    }
}
