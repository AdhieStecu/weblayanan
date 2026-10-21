<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiCommunicationTest extends TestCase
{
    public function test_api_ping_returns_success(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'connected',
            ])
            ->assertJsonStructure(['status', 'message', 'app', 'server_time']);
    }

    public function test_api_layanan_returns_data_list(): void
    {
        $response = $this->getJson('/api/layanan');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'nama', 'kategori', 'deskripsi', 'status', 'icon'],
                ],
            ]);
    }

    public function test_cors_allows_nuxt_frontend_origin(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'GET',
        ])->getJson('/api/ping');

        $response->assertStatus(200);
        $this->assertEquals('http://localhost:3000', $response->headers->get('Access-Control-Allow-Origin'));
    }
}
