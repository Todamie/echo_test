<?php

namespace Tests\Unit;

use App\Models\Author;
use Tests\TestCase;

class ApiAuthorsTest extends TestCase
{
    public function test_api_returns_authors_200(): void
    {
        $response = $this->get('/api/authors');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'last_name',
                        'first_name',
                        'middle_name',
                        'logo',
                        'birth_date',
                        'bio',
                        'slug',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]
        ]);
    }

    public function test_search_by_last_name_api_returns_authors_200(): void
    {
        $author = Author::create([
            'last_name' => 'author_search_api_test',
            'first_name' => 'author_search_api_test',
            'middle_name' => 'author_search_api_test',
        ]);

        $response = $this->post('/api/authors/search', [
            'search_author' => $author->last_name
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'last_name',
                        'first_name',
                        'middle_name',
                        'logo',
                        'birth_date',
                        'bio',
                        'slug',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]
        ]);
    }

    public function test_search_api_returns_error_if_search_is_empty_400(): void
    {
        $response = $this->post('/api/authors/search', [
            'search_author' => ''
        ]);

        $response->assertStatus(400);
    }

    public function test_search_api_returns_error_if_no_authors_found_404(): void
    {
        $response = $this->post('/api/authors/search', [
            'search_author' => 'sakljdnaoksdnaskdnasdokansdakodnasdlansdlakn'
        ]);

        $response->assertStatus(404);
    }

    public function test_api_returns_author_by_id_200(): void
    {
        $author = Author::create([
            'last_name' => 'author_search_api_test',
            'first_name' => 'author_search_api_test',
            'middle_name' => 'author_search_api_test',
        ]);

        $response = $this->get('/api/authors/' . $author->id);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'last_name',
                'first_name',
                'middle_name',
                'logo',
                'birth_date',
                'bio',
                'slug',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function test_api_returns_author_by_slug_200(): void
    {
        $author = Author::create([
            'last_name' => 'author_search_api_test',
            'first_name' => 'author_search_api_test',
            'middle_name' => 'author_search_api_test',
        ]);

        $response = $this->get('/api/authors/' . $author->slug);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'last_name',
                'first_name',
                'middle_name',
                'logo',
                'birth_date',
                'bio',
                'slug',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function test_api_returns_error_if_author_not_found_by_id_or_slug_404(): void
    {
        $response = $this->get('/api/authors/1234567890');

        $response->assertStatus(404);
    }
}
