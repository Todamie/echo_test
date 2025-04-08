<?php

namespace Tests\Unit;

use App\Models\Image;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class ApiImagePreviewTest extends TestCase
{
    public function test_api_returns_image_preview_200(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 400);
        
        $response = $this->post('/api/image', [
            'image' => $file,
            'width' => '200',
            'height' => '200',
            'method' => 'resize',
            'path' => 'images',
        ]);

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'name',
                'link',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_api_returns_error_when_validate_fails_image_preview_302(): void
    {

        $response = $this->post('/api/image', [
            'image' => 'https://dummyimage.com/600x400/fc9c0c/3d2e03&text=article1',
            'width' => 'aaa',
            'height' => 'aaa',
            'method' => 'aaa',
            'path' => 'aaa',
        ]);
        
        $response->assertStatus(302);

    }
}
