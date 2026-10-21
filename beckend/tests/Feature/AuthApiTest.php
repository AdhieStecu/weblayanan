<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Ahmad Dahlan',
            'email' => 'ahmad@student.ac.id',
            'password' => 'secret123',
            'nim' => '2210010055',
            'program_studi' => 'Teknik Informatika',
            'semester' => 4,
            'no_hp' => '081234567890',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'nim', 'program_studi', 'semester', 'no_hp'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@student.ac.id',
            'nim' => '2210010055',
        ]);
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@student.ac.id',
            'password' => Hash::make('password123'),
            'nim' => '2210010099',
            'program_studi' => 'Sistem Informasi',
            'semester' => 6,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'budi@student.ac.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'email' => 'budi@student.ac.id',
                    ],
                ],
            ])
            ->assertJsonStructure(['status', 'message', 'data' => ['user', 'token']]);
    }

    public function test_user_can_login_with_nim(): void
    {
        $user = User::create([
            'name' => 'Citra Lestari',
            'email' => 'citra@student.ac.id',
            'password' => Hash::make('mypassword'),
            'nim' => '2310020011',
            'program_studi' => 'Desain Komunikasi Visual',
            'semester' => 2,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'identifier' => '2310020011',
            'password' => 'mypassword',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'nim' => '2310020011',
                        'name' => 'Citra Lestari',
                    ],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'nonexistent@student.ac.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_authenticated_user_can_fetch_and_update_profile(): void
    {
        $user = User::create([
            'name' => 'Dewi Sartika',
            'email' => 'dewi@student.ac.id',
            'password' => Hash::make('pass12345'),
            'nim' => '2110030044',
            'program_studi' => 'Manajemen Informatika',
            'semester' => 5,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        // Fetch profile
        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/profile');

        $profileResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Dewi Sartika',
                    'email' => 'dewi@student.ac.id',
                    'nim' => '2110030044',
                ],
            ]);

        // Update profile
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/auth/profile', [
                'name' => 'Dewi Sartika, S.Kom.',
                'email' => 'dewi.sartika@student.ac.id',
                'nim' => '2110030044',
                'program_studi' => 'Sistem Informasi',
                'semester' => 7,
                'no_hp' => '085712345678',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Dewi Sartika, S.Kom.',
                    'email' => 'dewi.sartika@student.ac.id',
                    'semester' => 7,
                    'no_hp' => '085712345678',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Dewi Sartika, S.Kom.',
            'email' => 'dewi.sartika@student.ac.id',
            'semester' => 7,
        ]);

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
