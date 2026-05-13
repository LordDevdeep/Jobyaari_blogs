<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(8);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'short_description' => $this->faker->sentence(20),
            'content' => '<p>' . $this->faker->paragraph(10) . '</p>',
            'image' => null,
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'views' => $this->faker->numberBetween(0, 5000),
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
