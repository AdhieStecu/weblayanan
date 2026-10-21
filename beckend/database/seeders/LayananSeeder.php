<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = [
            [
                'nama' => 'Surat Keterangan Aktif Kuliah',
                'kategori' => 'Administrasi',
                'deskripsi' => 'Pengajuan surat resmi yang menyatakan mahasiswa aktif terdaftar di semester berjalan untuk keperluan beasiswa, BPJS, tunjangan orang tua, atau instansi luar.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'KRS semester aktif',
                    'Bukti pembayaran SPP/UKT terakhir',
                    'KTM (Kartu Tanda Mahasiswa)'
                ],
                'status' => 'Tersedia',
                'icon' => 'document-text',
            ],
            [
                'nama' => 'Pengajuan Cuti Akademik',
                'kategori' => 'Akademik',
                'deskripsi' => 'Permohonan istirahat studi sementara (maksimal 2 semester) dengan status terdaftar secara sah tanpa kehilangan hak studi.',
                'estimasi' => '3-5 Hari Kerja',
                'persyaratan' => [
                    'Surat permohonan bermaterai',
                    'Surat persetujuan orang tua/wali',
                    'Persetujuan Dosen Pembimbing Akademik (DPA)',
                    'Bebas tanggungan perpustakaan dan laboratorium'
                ],
                'status' => 'Tersedia',
                'icon' => 'pause-circle',
            ],
            [
                'nama' => 'Legalisir Dokumen Ijazah & Transkrip',
                'kategori' => 'Alumni & Kelulusan',
                'deskripsi' => 'Pengesahan dan legalisasi cap basah/digital untuk fotokopi ijazah, transkrip nilai akademik, dan sertifikat akreditasi program studi.',
                'estimasi' => '2 Hari Kerja',
                'persyaratan' => [
                    'Scan dokumen asli berwarna',
                    'Formulir permohonan legalisir',
                    'Bukti pembayaran biaya legalisir (bila ada)'
                ],
                'status' => 'Tersedia',
                'icon' => 'check-badge',
            ],
            [
                'nama' => 'Surat Rekomendasi Magang & Studi Independen',
                'kategori' => 'Kemahasiswaan',
                'deskripsi' => 'Surat pengantar dan rekomendasi resmi dari pimpinan fakultas untuk mengikuti program MSIB, magang mandiri BUMN/Swasta, atau pertukaran pelajar.',
                'estimasi' => '2-3 Hari Kerja',
                'persyaratan' => [
                    'Proposal atau TOR kegiatan magang',
                    'Surat penerimaan (LoA) bila sudah ada',
                    'Transkrip nilai sementara dengan IPK min. 3.00'
                ],
                'status' => 'Tersedia',
                'icon' => 'briefcase',
            ],
            [
                'nama' => 'Permohonan Penyesuaian / Keringanan UKT',
                'kategori' => 'Kemahasiswaan',
                'deskripsi' => 'Layanan bantuan finansial bagi mahasiswa dengan kendala ekonomi khusus, permohonan cicilan atau penurunan kelompok UKT.',
                'estimasi' => '5-7 Hari Kerja',
                'persyaratan' => [
                    'Surat keterangan tidak mampu (SKTM) kelurahan',
                    'Slip gaji atau surat keterangan penghasilan orang tua',
                    'Foto kondisi rumah dan bukti rekening listrik/air 3 bulan terakhir',
                    'Kartu Keluarga (KK)'
                ],
                'status' => 'Tersedia',
                'icon' => 'banknotes',
            ],
            [
                'nama' => 'Surat Keterangan Berkelakuan Baik',
                'kategori' => 'Administrasi',
                'deskripsi' => 'Surat resmi yang menerangkan bahwa mahasiswa tidak sedang dalam sanksi pelanggaran tata tertib kampus atau hukum.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'KTM aktif',
                    'Surat pernyataan bebas sanksi dari program studi'
                ],
                'status' => 'Tersedia',
                'icon' => 'shield-check',
            ],
        ];

        foreach ($layanan as $item) {
            Layanan::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
