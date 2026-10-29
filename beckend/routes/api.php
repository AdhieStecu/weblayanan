<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PengajuanLayananController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini tempat mendaftarkan rute API untuk komunikasi dengan frontend
| Nuxt (weblayananmahasiswa).
|
*/

// Tes koneksi frontend <-> backend
Route::get('/ping', function (\Illuminate\Http\Request $request) {
    if (! $request->wantsJson() && ! $request->query('format') && $request->acceptsHtml()) {
        return view('health');
    }

    return response()->json([
        'status' => 'connected',
        'message' => 'API Backend Laravel berhasil terhubung dengan Frontend Nuxt!',
        'app' => config('app.name'),
        'server_time' => now()->toIso8601String(),
    ]);
});

// Autentikasi Mahasiswa (Sanctum Token)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Manajemen Pengguna (Admin)
Route::get('/users', [AuthController::class, 'users']);

// Master Layanan Mahasiswa
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/layanan/{id}', [LayananController::class, 'show']);

// CRUD Pengajuan Layanan Mahasiswa
Route::get('/pengajuan/stats', [PengajuanLayananController::class, 'stats']);
Route::get('/pengajuan', [PengajuanLayananController::class, 'index']);
Route::post('/pengajuan', [PengajuanLayananController::class, 'store']);
Route::get('/pengajuan/{id}', [PengajuanLayananController::class, 'show']);
Route::put('/pengajuan/{id}', [PengajuanLayananController::class, 'update']);
Route::delete('/pengajuan/{id}', [PengajuanLayananController::class, 'destroy']);
Route::patch('/pengajuan/{id}/status', [PengajuanLayananController::class, 'updateStatus']);
Route::post('/pengajuan/{id}/approve', [PengajuanLayananController::class, 'approve']);
Route::post('/pengajuan/{id}/reject', [PengajuanLayananController::class, 'reject']);

// Content Management System (Landing Page & Postman Image Upload)
Route::prefix('cms')->group(function () {
    Route::get('/content', [CmsController::class, 'index']);
    Route::get('/content/{key}', [CmsController::class, 'show']);
    Route::post('/content', [CmsController::class, 'update']);
    Route::post('/upload', [CmsController::class, 'uploadImage']);
    Route::post('/reset', [CmsController::class, 'reset']);
});

// Endpoint data user terotentikasi (Sanctum fallback)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

