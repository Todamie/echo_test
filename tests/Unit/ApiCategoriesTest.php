<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;

class ApiCategoriesTest extends TestCase
{
    public function test_api_returns_categories_200(): void
    {
        $response = $this->get('/api/categories');

        $response->assertStatus(200);
    }

    public function test_api_returns_category_by_id_200(): void
    {
        $category = Category::create([
            'name' => 'category_search_api_test',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=category1',
            'description' => 'Описание категории 1',
        ]);

        $response = $this->get('/api/categories/' . $category->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'category' => [
                    'id',
                    'name',
                    'image',
                    'description',
                    'slug',
                    '_lft',
                    '_rgt',
                    'parent_id',
                    'created_at',
                    'updated_at',
                ],
                'children' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'description',
                        'slug',
                        '_lft',
                        '_rgt',
                        'parent_id',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'ancestors' => [
                    '*' => [
                        'id',
                        'name',
                        'image',
                        'description',
                        'slug',
                        '_lft',
                        '_rgt',
                        'parent_id',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]
        ]);
    }

    public function test_api_returns_category_by_slug_200(): void
    {
        $category = Category::create([
            'name' => 'category_search_api_test',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=category1',
            'description' => 'Описание категории 1',
        ]);

        $response = $this->get('/api/categories/' . $category->slug);

        $response->assertStatus(200);
    }

    public function test_api_returns_error_if_category_not_found_by_id_or_slug_404(): void
    {
        $response = $this->get('/api/categories/1234567890');

        $response->assertStatus(404);
    }
}
