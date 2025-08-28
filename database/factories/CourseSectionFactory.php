<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseSection>
 */
class CourseSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'order_index' => fake()->numberBetween(1, 10),
            'is_active' => true,
            'price' => null,
            'discount_price' => null,
            'is_purchasable_separately' => false,
        ];
    }

    /**
     * Indicate that the section is purchasable separately.
     */
    public function purchasable(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 10, 50),
            'discount_price' => fake()->optional()->randomFloat(2, 5, 30),
            'is_purchasable_separately' => true,
        ]);
    }

    /**
     * Indicate that the section is free.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'discount_price' => null,
            'is_purchasable_separately' => true,
        ]);
    }

    /**
     * Indicate that the section is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
