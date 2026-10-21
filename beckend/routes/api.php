<?php

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
Route::get('/ping', function () {
    return response()->json([
        'status' => 'connected',
        'message' => 'API Backend Laravel berhasil terhubung dengan Frontend Nuxt!',
        'app' => config('app.name'),
        'server_time' => now()->toIso8601String(),
    ]);
});

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

// Endpoint data user terotentikasi (Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
