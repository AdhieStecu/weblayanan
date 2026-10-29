<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    /**
     * Ambil seluruh konfigurasi konten Landing Page CMS.
     */
    public function index(): JsonResponse
    {
        $all = LandingContent::all();
        $mapped = [];

        foreach ($all as $item) {
            $mapped[$item->key] = $item->value;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Konten CMS Landing Page berhasil diambil.',
            'data' => [
                'settings' => $mapped,
                'items' => $all,
            ],
        ]);
    }

    /**
     * Ambil data satu kunci CMS tertentu.
     */
    public function show(string $key): JsonResponse
    {
        $content = LandingContent::where('key', $key)->first();

        if (! $content) {
            return response()->json([
                'status' => 'error',
                'message' => "Konten dengan kunci '{$key}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $content,
        ]);
    }

    /**
     * Update konten teks/pengaturan CMS via JSON body.
     */
    public function update(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (empty($payload)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payload JSON tidak boleh kosong.',
            ], 422);
        }

        // Clean up any previously inserted artifact keys if exist
        LandingContent::whereIn('key', ['key', 'value'])->delete();

        $updated = [];

        // Support format {"key": "hero_title", "value": "..."}
        if (isset($payload['key']) && isset($payload['value'])) {
            $k = (string)$payload['key'];
            $v = (string)$payload['value'];
            $item = LandingContent::updateOrCreate(
                ['key' => $k],
                [
                    'value' => $v,
                    'type' => 'text',
                ]
            );
            $updated[$k] = $item->value;
        } else {
            // Support format {"hero_title": "...", "notice_text": "..."}
            foreach ($payload as $key => $val) {
                if ($key === '_token' || $key === 'key' || $key === 'value') continue;

                $item = LandingContent::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => is_array($val) ? json_encode($val) : (string)$val,
                        'type' => is_array($val) ? 'json' : 'text',
                    ]
                );

                $updated[$key] = $item->value;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Konten CMS berhasil diperbarui!',
            'data' => $updated,
        ]);
    }

    /**
     * Upload gambar (Banner Hero, Icon Layanan, dll) via Postman (multipart/form-data).
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:10240'],
            'key' => ['nullable', 'string', 'max:100'],
        ], [
            'file.required' => 'File gambar wajib diunggah.',
            'file.image' => 'File harus berupa format gambar valid.',
            'file.mimes' => 'Format gambar yang didukung: JPG, JPEG, PNG, WEBP, GIF, SVG.',
            'file.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $file = $request->file('file');
        $key = $request->input('key', 'hero_banner_image');

        $destinationPath = public_path('uploads/landing');
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $extension;

        $file->move($destinationPath, $filename);

        $baseUrl = rtrim(config('app.url', url('/')), '/');
        // Jika request host mengandung port (misal localhost:8000), prioritaskan dari request aktif
        if ($request->header('host')) {
            $scheme = $request->isSecure() ? 'https' : 'http';
            $baseUrl = $scheme . '://' . $request->header('host');
        }

        $fileUrl = $baseUrl . '/uploads/landing/' . $filename;

        $item = LandingContent::updateOrCreate(
            ['key' => $key],
            [
                'value' => $fileUrl,
                'type' => 'image',
                'group' => 'hero',
                'description' => "Gambar diunggah via API Postman ({$file->getClientOriginalName()})",
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Gambar untuk '{$key}' berhasil diupload dan aktif di Landing Page!",
            'data' => [
                'key' => $key,
                'url' => $fileUrl,
                'filename' => $filename,
                'size' => filesize($destinationPath . '/' . $filename),
                'content' => $item,
            ],
        ], 200);
    }

    /**
     * Reset seluruh konten Landing Page ke template bawaan UMPKU.
     */
    public function reset(): JsonResponse
    {
        $defaultUrl = rtrim(config('app.url', url('/')), '/') . '/uploads/landing/hero-banner-default.jpg';

        $defaults = [
            'hero_banner_image' => $defaultUrl,
            'hero_title' => 'Layanan Mahasiswa',
            'hero_subtitle' => 'Portal Layanan Terpadu Administrasi, Riset & Akademik Mahasiswa',
            'faculty_name' => 'UMPKU',
            'campus_name' => '',
            'notice_text' => 'Sebelum mengisi formulir, pastikan sudah login menggunakan akun email mahasiswa Anda.',
            'announcement_badge' => 'Layanan Terakreditasi Digital & Terintegrasi',
            'contact_whatsapp' => '081234567890',
            'contact_email' => 'layanan@umpku.ac.id',
        ];

        foreach ($defaults as $k => $v) {
            LandingContent::updateOrCreate(
                ['key' => $k],
                [
                    'value' => $v,
                    'type' => $k === 'hero_banner_image' ? 'image' : 'text',
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Konten CMS Landing Page berhasil di-reset ke bawaan UMPKU!',
            'data' => $defaults,
        ]);
    }
}
