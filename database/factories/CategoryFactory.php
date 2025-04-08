<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $name = fake()->catchPhrase();
        return [
            'name' => fake()->catchPhrase(),
            'image' => 'https://dummyimage.com/600x400/' . 
            str_replace('#', '', fake()->hexColor()) . '/' . 
            str_replace('#', '', fake()->hexColor()) .
            '&text='.fake()->word(),
            'description' => fake()->text(),
        ];
    }
}
