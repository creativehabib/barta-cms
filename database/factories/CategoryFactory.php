<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $bn = fake()->words(2, true);
        $en = fake()->words(2, true);

        return [
            'name' => ['bn' => $bn, 'en' => $en],
            'description' => ['bn' => fake()->sentence(), 'en' => fake()->sentence()],
            'color' => fake()->hexColor(),
            'position' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'show_in_menu' => true,
        ];
    }
}
