<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'image' => 'https://dummyimage.com/600x400/' . 
            str_replace('#', '', fake()->hexColor()) . '/' . 
            str_replace('#', '', fake()->hexColor()) .
            '&text='.fake()->word(),
            'announcement' => fake()->text(),
            'content' => fake()->text(),
            'author_id' => Author::factory(),
        ];
    }
}
