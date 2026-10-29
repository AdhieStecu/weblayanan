<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = [
            // 12 Layanan Utama sesuai UMPKU
            [
                'nama' => 'Permohonan Sertifikat PKM',
                'kategori' => 'Prestasi & Kompetisi',
                'deskripsi' => 'Pengajuan penerbitan sertifikat resmi bagi mahasiswa yang telah menyusun dan lolos seleksi Program Kreativitas Mahasiswa (PKM).',
                'estimasi' => '2 Hari Kerja',
                'persyaratan' => [
                    'Proposal PKM yang disahkan Dosen Pembimbing',
                    'Bukti lolos pendanaan atau apresiasi universitas',
                    'KTM Mahasiswa dan Anggota Tim'
                ],
                'status' => 'Tersedia',
                'icon' => 'certificate',
            ],
            [
                'nama' => 'Permohonan Surat Keterangan Lulus (SKL) & Input Nilai Skripsi',
                'kategori' => 'Akademik & Kelulusan',
                'deskripsi' => 'Pengajuan SKL resmi dan input nilai tugas akhir/skripsi bagi mahasiswa yang telah menyelesaikan pendadaran dan memenuhi minimal 144 SKS.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'Berita acara kelulusan ujian pendadaran/skripsi',
                    'Transkrip nilai sementara (min 144 SKS)',
                    'Surat bebas tanggungan perpustakaan dan laboratorium',
                    'Bukti pelunasan SPP/DPP tingkat akhir'
                ],
                'status' => 'Tersedia',
                'icon' => 'academic-cap',
            ],
            [
                'nama' => 'Permohonan Surat Izin Riset',
                'kategori' => 'Penelitian & Tugas Akhir',
                'deskripsi' => 'Pengajuan surat pengantar resmi dari pimpinan fakultas untuk pengambilan data atau observasi penelitian skripsi pada instansi/perusahaan tujuan.',
                'estimasi' => '1-2 Hari Kerja',
                'persyaratan' => [
                    'Proposal penelitian yang telah disetujui Dosen Pembimbing',
                    'Surat persetujuan judul skripsi',
                    'Nama pejabat dan alamat lengkap instansi tujuan riset'
                ],
                'status' => 'Tersedia',
                'icon' => 'magnifying-glass',
            ],
            [
                'nama' => 'Permohonan Surat Pengantar Magang (Kelompok)',
                'kategori' => 'Magang & PKL',
                'deskripsi' => 'Surat pengantar resmi bagi mahasiswa yang mengajukan kerja praktik/magang secara berkelompok pada perusahaan atau instansi mitra.',
                'estimasi' => '1-2 Hari Kerja',
                'persyaratan' => [
                    'Daftar lengkap nama dan NIM anggota kelompok',
                    'Proposal pengajuan magang kelompok',
                    'Transkrip nilai sementara setiap anggota (IPK min 2.75)'
                ],
                'status' => 'Tersedia',
                'icon' => 'user-group',
            ],
            [
                'nama' => 'Permohonan Surat Pengantar Magang (Individu)',
                'kategori' => 'Magang & PKL',
                'deskripsi' => 'Penerbitan surat pengantar resmi fakultas untuk pendaftaran magang mandiri atau program magang industri tingkat individu.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'Transkrip nilai sementara terbaru',
                    'Data nama instansi/perusahaan dan bagian yang dituju',
                    'KRS semester aktif yang mencantumkan mata kuliah Magang/PKL'
                ],
                'status' => 'Tersedia',
                'icon' => 'briefcase',
            ],
            [
                'nama' => 'Permohonan Surat Pembatalan Magang',
                'kategori' => 'Magang & PKL',
                'deskripsi' => 'Surat permohonan resmi pembatalan magang/PKL yang telah disetujui sebelumnya karena alasan khusus yang dapat dipertanggungjawabkan.',
                'estimasi' => '1-2 Hari Kerja',
                'persyaratan' => [
                    'Salinan surat pengantar magang yang sebelumnya diterbitkan',
                    'Surat pernyataan alasan pembatalan bermaterai',
                    'Surat persetujuan dari Dosen Pembimbing Akademik/Magang'
                ],
                'status' => 'Tersedia',
                'icon' => 'x-circle',
            ],
            [
                'nama' => 'Permohonan Sertifikat Kolaborasi Penelitian Dosen',
                'kategori' => 'Penelitian & Publikasi',
                'deskripsi' => 'Penerbitan sertifikat penghargaan dan pengakuan bagi mahasiswa yang terlibat aktif dalam proyek riset atau hibah penelitian dosen.',
                'estimasi' => '2-3 Hari Kerja',
                'persyaratan' => [
                    'Surat rekomendasi dari dosen peneliti utama',
                    'Bukti kontrak penelitian atau luaran publikasi (jurnal/prosiding)',
                    'KTM Mahasiswa'
                ],
                'status' => 'Tersedia',
                'icon' => 'sparkles',
            ],
            [
                'nama' => 'Permohonan Surat Tugas Mahasiswa',
                'kategori' => 'Kemahasiswaan & Delegasi',
                'deskripsi' => 'Surat tugas formal dari fakultas/universitas untuk menghadiri kegiatan ilmiah, workshop, seminar nasional, atau penugasan resmi institusi.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'Surat undangan resmi dari penyelenggara kegiatan',
                    'Rundown acara kegiatan',
                    'Persetujuan Ketua Program Studi'
                ],
                'status' => 'Tersedia',
                'icon' => 'clipboard-document-check',
            ],
            [
                'nama' => 'Permohonan Surat Tugas Mata Kuliah',
                'kategori' => 'Akademik',
                'deskripsi' => 'Penerbitan surat pengantar studi lapangan atau tugas survei lapangan dalam rangka pemenuhan tugas perkuliahan mata kuliah tertentu.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'Deskripsi penugasan dari Dosen Pengampu Mata Kuliah',
                    'Daftar mahasiswa yang ditugaskan',
                    'Lokasi dan estimasi jadwal survei'
                ],
                'status' => 'Tersedia',
                'icon' => 'document-text',
            ],
            [
                'nama' => 'Permohonan Surat Tugas Mahasiswa (Team)',
                'kategori' => 'Kemahasiswaan & Delegasi',
                'deskripsi' => 'Surat tugas delegasi tim mahasiswa yang mewakili fakultas atau kampus dalam perlombaan, kompetisi ilmiah, atau festival kejuaraan.',
                'estimasi' => '1-2 Hari Kerja',
                'persyaratan' => [
                    'Surat undangan atau bukti lolos seleksi kompetisi',
                    'Susunan tim dan proposal keikutsertaan delegasi',
                    'Rekomendasi Dosen Pendamping / Pembina'
                ],
                'status' => 'Tersedia',
                'icon' => 'users',
            ],
            [
                'nama' => 'Permohonan Surat Izin Tidak Mengikuti Magang',
                'kategori' => 'Magang & PKL',
                'deskripsi' => 'Surat izin dispensasi resmi untuk tidak masuk sementara dalam kegiatan magang karena urusan akademik, kesehatan, atau keluarga mendesak.',
                'estimasi' => '1 Hari Kerja',
                'persyaratan' => [
                    'Surat keterangan dokter (bila sakit) atau bukti urusan mendesak',
                    'Konfirmasi pemberitahuan kepada mentor lapangan perusahaan',
                    'Formulir izin dispensasi yang ditandatangani'
                ],
                'status' => 'Tersedia',
                'icon' => 'calendar-days',
            ],
            [
                'nama' => 'Permohonan Surat Rekomendasi Mahasiswa',
                'kategori' => 'Beasiswa & Karir',
                'deskripsi' => 'Penerbitan surat rekomendasi resmi dari Dekan atau Wakil Dekan untuk keperluan pendaftaran beasiswa bergengsi, studi lanjut, atau karir profesional.',
                'estimasi' => '2 Hari Kerja',
                'persyaratan' => [
                    'Transkrip nilai sementara dengan IPK minimal 3.25',
                    'Sertifikat prestasi pendukung (akademik/non-akademik)',
                    'Formulir format rekomendasi dari pihak pemberi beasiswa'
                ],
                'status' => 'Tersedia',
                'icon' => 'hand-thumb-up',
            ],

            // Layanan Tambahan yang Sudah Ada di Sistem
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
        ];

        foreach ($layanan as $item) {
            Layanan::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
