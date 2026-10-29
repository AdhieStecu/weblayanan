<?php

namespace Database\Seeders;

use App\Models\LandingContent;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $defaultUrl = config('app.url', 'http://localhost:8000') . '/uploads/landing/hero-banner-default.jpg';

        $contents = [
            [
                'key' => 'hero_banner_image',
                'value' => $defaultUrl,
                'type' => 'image',
                'group' => 'hero',
                'description' => 'Gambar utama banner latar hero section Layanan Mahasiswa',
            ],
            [
                'key' => 'hero_title',
                'value' => 'Layanan Mahasiswa',
                'type' => 'text',
                'group' => 'hero',
                'description' => 'Judul besar pada banner landing page',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Portal Layanan Terpadu Administrasi, Riset & Akademik Mahasiswa',
                'type' => 'text',
                'group' => 'hero',
                'description' => 'Subjudul pengantar di bawah judul hero',
            ],
            [
                'key' => 'faculty_name',
                'value' => 'UMPKU',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama Fakultas / Institusi yang ditampilkan pada header',
            ],
            [
                'key' => 'campus_name',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama Perguruan Tinggi / Universitas',
            ],
            [
                'key' => 'notice_text',
                'value' => 'Sebelum mengisi formulir, pastikan sudah login menggunakan akun email mahasiswa Anda.',
                'type' => 'text',
                'group' => 'notice',
                'description' => 'Pesan pemberitahuan penting sebelum pengajuan formulir',
            ],
            [
                'key' => 'announcement_badge',
                'value' => 'Layanan Terakreditasi Digital & Terintegrasi',
                'type' => 'text',
                'group' => 'notice',
                'description' => 'Badge kecil pada alert notice banner',
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '081234567890',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Nomor Helpdesk WhatsApp Tata Usaha',
            ],
            [
                'key' => 'contact_email',
                'value' => 'layanan@umpku.ac.id',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Email resmi pengaduan dan layanan',
            ],
        ];

        foreach ($contents as $item) {
            LandingContent::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
