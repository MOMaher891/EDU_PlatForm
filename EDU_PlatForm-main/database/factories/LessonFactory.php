<?php

namespace Database\Factories;

use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraphs(3, true),
            'order_index' => $this->faker->numberBetween(1, 100),
            'video_duration' => $this->faker->numberBetween(300, 3600), // 5-60 minutes
            'file_path' => null,
            'video_url' => null,
            'file_type' => 'video',
            'is_free' => false,
            'is_active' => true,
            'section_id' => CourseSection::factory(),
        ];
    }

    public function withVideo()
    {
        return $this->state(function (array $attributes) {
            return [
                'file_path' => 'videos/lesson-' . $this->faker->uuid . '.mp4',
                'file_name' => 'lesson-video.mp4',
                'file_size' => $this->faker->numberBetween(10000000, 100000000), // 10-100 MB
                'mime_type' => 'video/mp4',
            ];
        });
    }

    public function withExternalVideo()
    {
        return $this->state(function (array $attributes) {
            return [
                'video_url' => 'https://www.youtube.com/watch?v=' . $this->faker->regexify('[A-Za-z0-9]{11}'),
            ];
        });
    }

    public function unpublished()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => false,
            ];
        });
    }
}
