<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Tests\TestCase;

class ApiArticlesTest extends TestCase
{
    public function test_api_returns_articles_200(): void
    {
        $response = $this->get('/api/articles');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'author_id',
                        'title',
                        'image',
                        'announcement',
                        'content',
                        'slug',
                        'created_at',
                        'updated_at',
                        'categories',
                        'author'
                    ]
                ]
            ]
        ]);
    }

    public function test_search_by_title_api_returns_articles_200(): void
    {
        $article = Article::create([
            'title' => 'Тестовая статья',
            'slug' => 'test-article',
            'content' => 'Тестовая статья',
            'author_id' => 1,
            'announcement' => 'Тестовая статья',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=article1',
        ]);

        $response = $this->post('/api/articles/search', [
            'search_article' => $article->title
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'author_id',
                        'title',
                        'image',
                        'announcement',
                        'content',
                        'slug',
                        'created_at',
                        'updated_at',
                        'categories',
                        'author'
                    ]
                ]
            ]
        ]);
    }

    public function test_search_by_category_api_returns_articles_200(): void
    {
        $category = Category::create([
            'name' => 'category_search_api_test',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=category1',
            'description' => 'Описание категории 1',
        ]);
        Article::create([
            'title' => 'Тестовая статья',
            'slug' => 'test-article',
            'content' => 'Тестовая статья',
            'author_id' => 1,
            'announcement' => 'Тестовая статья',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=article1',
        ])->categories()->attach($category);

        $response = $this->post('/api/articles/search', [
            'search_article' => $category->name
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'author_id',
                        'title',
                        'image',
                        'announcement',
                        'content',
                        'slug',
                        'created_at',
                        'updated_at',
                        'categories',
                        'author'
                    ]
                ]
            ]
        ]);
    }

    public function test_search_by_author_api_returns_articles_200(): void
    {
        $author = Author::create([
            'last_name' => 'author_search_api_test',
            'first_name' => 'author_search_api_test',
            'middle_name' => 'author_search_api_test',
        ]);
        Article::create([
            'title' => 'Тестовая статья',
            'slug' => 'test-article',
            'content' => 'Тестовая статья',
            'author_id' => $author->id,
            'announcement' => 'Тестовая статья',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=article1',
        ]);

        $response = $this->post('/api/articles/search', [
            'search_article' => $author->last_name
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'author_id',
                        'title',
                        'image',
                        'announcement',
                        'content',
                        'slug',
                        'created_at',
                        'updated_at',
                        'categories',
                        'author'
                    ]
                ]
            ]
        ]);
    }

    public function test_search_api_returns_error_if_search_is_empty_400(): void
    {
        $response = $this->post('/api/articles/search', [
            'search_article' => ''
        ]);

        $response->assertStatus(400);
    }

    public function test_search_api_returns_error_if_no_articles_found_404(): void
    {
        $response = $this->post('/api/articles/search', [
            'search_article' => 'sakljdnaoksdna;lsda;sldmlmgldkmfdklggmgaskdnasdokansdakodnasdlansdlakn'
        ]);

        $response->assertStatus(404);
    }

    public function test_api_returns_article_by_id_200(): void
    {
        $article = Article::create([
            'title' => 'Тестовая статья',
            'slug' => 'test-article',
            'content' => 'Тестовая статья',
            'author_id' => 1,
            'announcement' => 'Тестовая статья',
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=article1',
        ]);

        $response = $this->get('/api/articles/' . $article->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'author_id',
                'title',
                'image',
                'announcement',
                'content',
                'slug',
                'created_at',
                'updated_at',
                'categories',
                'author'
            ]
        ]);
    }

    public function test_api_returns_article_by_slug_200(): void
    {
        $article = Article::create([
            'title' => 'Тестовая статья',
            'slug' => 'test-article',
            'content' => 'Тестовая статья',
            'author_id' => 1,
            'announcement' => 'Тестовая статья',
        ]);

        $response = $this->get('/api/articles/' . $article->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'author_id',
                'title',
                'image',
                'announcement',
                'content',
                'slug',
                'created_at',
                'updated_at',
                'categories',
                'author'
            ]
        ]);
    }

    public function test_api_returns_error_if_article_not_found_by_id_or_slug_404(): void
    {
        $response = $this->get('/api/articles/1234567890');

        $response->assertStatus(404);
    }
}
