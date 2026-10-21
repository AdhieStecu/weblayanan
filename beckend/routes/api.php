<?php

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

// Contoh endpoint layanan mahasiswa
Route::get('/layanan', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            [
                'id' => 1,
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'kategori' => 'Administrasi',
                'deskripsi' => 'Pengajuan surat keterangan aktif kuliah untuk keperluan beasiswa, magang, atau instansi.',
                'status' => 'Tersedia',
                'icon' => 'document'
            ],
            [
                'id' => 2,
                'nama' => 'Pengajuan Cuti Akademik',
                'kategori' => 'Akademik',
                'deskripsi' => 'Permohonan istirahat studi sementara sesuai ketentuan akademik kampus.',
                'status' => 'Tersedia',
                'icon' => 'pause'
            ],
            [
                'id' => 3,
                'nama' => 'Legalisir Dokumen Akademik',
                'kategori' => 'Alumni & Kelulusan',
                'deskripsi' => 'Pengajuan legalisasi ijazah, transkrip nilai, dan sertifikat akreditasi online.',
                'status' => 'Tersedia',
                'icon' => 'check-badge'
            ],
            [
                'id' => 4,
                'nama' => 'Konseling & Beasiswa Mahasiswa',
                'kategori' => 'Kemahasiswaan',
                'deskripsi' => 'Layanan bimbingan konseling dan pendaftaran beasiswa internal/eksternal.',
                'status' => 'Tersedia',
                'icon' => 'academic-cap'
            ]
        ]
    ]);
});

// Endpoint data user terotentikasi (Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
