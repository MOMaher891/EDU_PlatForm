<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->slug(),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 10, 200),
            'discount_price' => fake()->optional()->randomFloat(2, 5, 100),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_hours' => fake()->numberBetween(1, 50),
            'category_id' => Category::factory(),
            'instructor_id' => User::factory()->instructor(),
            'is_published' => true,
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
            'is_deleted' => false,
            'is_archived' => false,
        ];
    }

    /**
     * Indicate that the course is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'discount_price' => null,
        ]);
    }

    /**
     * Indicate that the course is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the course is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
