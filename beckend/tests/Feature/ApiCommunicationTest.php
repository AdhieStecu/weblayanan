<?php

namespace Tests\Feature;

use App\Models\PengajuanLayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCommunicationTest extends TestCase
{
    use RefreshDatabase;
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

    public function test_api_pengajuan_stats_returns_counts(): void
    {
        $response = $this->getJson('/api/pengajuan/stats');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'data' => ['total', 'menunggu', 'diproses', 'selesai', 'ditolak'],
            ]);
    }

    public function test_api_pengajuan_crud_cycle(): void
    {
        // 1. CREATE (POST)
        $payload = [
            'nim' => '2209990001',
            'nama_mahasiswa' => 'Test Mahasiswa CRUD',
            'email' => 'mahasiswa.test@student.ac.id',
            'no_hp' => '081299887766',
            'fakultas' => 'Fakultas Teknik',
            'program_studi' => 'Teknik Informatika',
            'semester' => 4,
            'jenis_layanan' => 'Surat Keterangan Aktif Kuliah',
            'keperluan' => 'Keperluan pendaftaran beasiswa riset.',
        ];

        $postResponse = $this->postJson('/api/pengajuan', $payload);
        $postResponse->assertStatus(201)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'data' => ['id', 'kode_tiket', 'nim', 'nama_mahasiswa', 'status'],
            ]);

        $createdId = $postResponse->json('data.id');
        $this->assertNotNull($createdId);

        // 2. READ (GET ONE & LIST)
        $getResponse = $this->getJson("/api/pengajuan/{$createdId}");
        $getResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $createdId,
                    'nim' => '2209990001',
                ],
            ]);

        // 3. UPDATE (PUT)
        $updatePayload = [
            'keperluan' => 'Keperluan pendaftaran beasiswa riset telah diperbarui.',
            'catatan_petugas' => 'Berkas mahasiswa valid dan lengkap.',
            'status' => 'diproses',
        ];

        $putResponse = $this->putJson("/api/pengajuan/{$createdId}", $updatePayload);
        $putResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $createdId,
                    'status' => 'diproses',
                ],
            ]);

        // 4. PATCH STATUS
        $patchResponse = $this->patchJson("/api/pengajuan/{$createdId}/status", [
            'status' => 'selesai',
            'catatan_petugas' => 'Selesai disetujui.',
        ]);
        $patchResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'status' => 'selesai',
                ],
            ]);

        // 5. DELETE (DELETE)
        $deleteResponse = $this->deleteJson("/api/pengajuan/{$createdId}");
        $deleteResponse->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // Verify deleted
        $getDeleted = $this->getJson("/api/pengajuan/{$createdId}");
        $getDeleted->assertStatus(404);
    }

    public function test_api_pengajuan_validation_error(): void
    {
        $response = $this->postJson('/api/pengajuan', [
            'nim' => '',
            'nama_mahasiswa' => '',
            'email' => 'bukan-email',
        ]);

        $response->assertStatus(422)
            ->assertJson(['status' => 'error'])
            ->assertJsonStructure(['errors' => ['nim', 'nama_mahasiswa', 'email']]);
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
