<?php

namespace Tests\Unit;

use Tests\TestCase;

class ApiTest extends TestCase
{
    
    public function test_app_is_running(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
