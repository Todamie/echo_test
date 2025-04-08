<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'logo' => 'https://dummyimage.com/600x400/' . 
            str_replace('#', '', fake()->hexColor()) . '/' . 
            str_replace('#', '', fake()->hexColor()) .
            '&text='.fake()->word(),
            'birth_date' => fake()->date(),
            'bio' => fake()->text(),
        ];
    }
}
