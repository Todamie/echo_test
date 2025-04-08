<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Тестовая категория и подкатегория
        $root1 = Category::create([
            'name' => 'Категория 1',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=category1',
            'description' => 'Описание категории 1',
        ]);

        $child1 = Category::create([
            'name' => 'Под 1.1',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=podcategory2',
            'description' => 'Описание под',
        ]);
        $child1->appendToNode($root1)->save();

        

        // Создание категорий
        Category::factory(20)->create()->each(function ($category) {
            
            // Создание подкатегорий для каждой категории
            Category::factory(10)->create()->each(function ($subcategory) use ($category) {
                $subcategory->appendToNode($category)->save();

                // Создание подкатегорий для каждой подкатегории
                Category::factory(5)->create()->each(function ($subsubcategory) use ($subcategory) {
                    $subsubcategory->appendToNode($subcategory)->save();
                });
            });
        });

        // Создание статей и привязка к случайным категориям
        Article::factory(10000)->create()->each(function ($article) {
            $article->categories()->attach(
                Category::inRandomOrder()->limit(rand(1, 3))->pluck('id')
            );
        });
    }
}
