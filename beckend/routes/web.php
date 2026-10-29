<?php

use App\Http\Controllers\Admin\AdminWebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    if (request()->wantsJson()) {
        return response()->json([
            'status' => 'online',
            'service' => 'Backend API SiLayanan Mahasiswa UMPKU',
            'framework' => 'Laravel ' . app()->version(),
            'php_version' => PHP_VERSION,
            'frontend_url' => config('app.frontend_url', 'http://localhost:3000'),
            'api_ping' => url('/api/ping'),
            'admin_portal' => url('/admin/login'),
        ]);
    }

    $dbStatus = false;
    try {
        DB::connection()->getPdo();
        $dbStatus = true;
    } catch (\Throwable $e) {
        $dbStatus = false;
    }

    return view('welcome', [
        'dbStatus' => $dbStatus,
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Portal Khusus Administrator (Backend Only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Autentikasi Admin
    Route::get('/login', [AdminWebController::class, 'loginView'])->name('admin.login');
    Route::post('/login', [AdminWebController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminWebController::class, 'logout'])->name('admin.logout');

    // Area Terproteksi Khusus Administrator
    Route::middleware(['web', 'admin'])->group(function () {
        Route::get('/', [AdminWebController::class, 'dashboard'])->name('admin.index');
        Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');

        // Manajemen Pengajuan Mahasiswa (Approval & Rejection)
        Route::get('/pengajuan', [AdminWebController::class, 'pengajuan'])->name('admin.pengajuan');
        Route::post('/pengajuan/{id}/approve', [AdminWebController::class, 'approvePengajuan'])->name('admin.pengajuan.approve');
        Route::post('/pengajuan/{id}/reject', [AdminWebController::class, 'rejectPengajuan'])->name('admin.pengajuan.reject');
        Route::post('/pengajuan/{id}/status', [AdminWebController::class, 'updateStatusPengajuan'])->name('admin.pengajuan.status');
        Route::delete('/pengajuan/{id}', [AdminWebController::class, 'deletePengajuan'])->name('admin.pengajuan.delete');

        // Manajemen Pengguna / Mahasiswa
        Route::get('/users', [AdminWebController::class, 'users'])->name('admin.users');

        // Manajemen Master Layanan
        Route::get('/layanan', [AdminWebController::class, 'layanan'])->name('admin.layanan');
        Route::post('/layanan/{id}/toggle', [AdminWebController::class, 'toggleLayanan'])->name('admin.layanan.toggle');

        // CMS Banner Hero & Konten Landing
        Route::get('/cms', [AdminWebController::class, 'cms'])->name('admin.cms');
        Route::post('/cms', [AdminWebController::class, 'updateCms'])->name('admin.cms.update');

        // Dokumentasi & Akses API
        Route::get('/api', [AdminWebController::class, 'apiDocs'])->name('admin.api');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
