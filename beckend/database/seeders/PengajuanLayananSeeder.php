<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\PengajuanLayanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengajuanLayananSeeder extends Seeder
{
    public function run(): void
    {
        $layananAktif = Layanan::where('nama', 'Surat Keterangan Aktif Kuliah')->first();
        $layananCuti = Layanan::where('nama', 'Pengajuan Cuti Akademik')->first();
        $layananLegalisir = Layanan::where('nama', 'Legalisir Dokumen Ijazah & Transkrip')->first();
        $layananMagang = Layanan::where('nama', 'Surat Rekomendasi Magang & Studi Independen')->first();
        $layananUkt = Layanan::where('nama', 'Permohonan Penyesuaian / Keringanan UKT')->first();

        $samples = [
            [
                'kode_tiket' => 'PLM-202610-0001',
                'nim' => '2201010042',
                'nama_mahasiswa' => 'Rian Pratama',
                'email' => 'rian.pratama@student.kampus.ac.id',
                'no_hp' => '081234567890',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'program_studi' => 'Teknik Informatika',
                'semester' => 5,
                'layanan_id' => $layananAktif?->id,
                'jenis_layanan' => 'Surat Keterangan Aktif Kuliah',
                'keperluan' => 'Pengajuan beasiswa Djarum Plus semester ganjil 2026/2027.',
                'dokumen_lampiran' => 'krs_semester_5_rian.pdf',
                'status' => 'selesai',
                'catatan_petugas' => 'Surat telah diterbitkan dan ditandatangani secara elektronik (BSrE). Silakan unduh dokumen pada email terdaftar.',
                'tanggal_pengajuan' => Carbon::now()->subDays(4),
            ],
            [
                'kode_tiket' => 'PLM-202610-0002',
                'nim' => '2302020115',
                'nama_mahasiswa' => 'Anisa Rahmawati',
                'email' => 'anisa.rahma@student.kampus.ac.id',
                'no_hp' => '085712348899',
                'fakultas' => 'Fakultas Ekonomi & Bisnis',
                'program_studi' => 'Manajemen Bisnis',
                'semester' => 3,
                'layanan_id' => $layananMagang?->id,
                'jenis_layanan' => 'Surat Rekomendasi Magang & Studi Independen',
                'keperluan' => 'Pendaftaran program Magang Bersertifikat Kampus Merdeka di PT Telkom Indonesia.',
                'dokumen_lampiran' => 'transkrip_anisa_ipk385.pdf',
                'status' => 'diproses',
                'catatan_petugas' => 'Sedang diverifikasi oleh Wakil Dekan I Bidang Akademik.',
                'tanggal_pengajuan' => Carbon::now()->subDays(2),
            ],
            [
                'kode_tiket' => 'PLM-202610-0003',
                'nim' => '2101050089',
                'nama_mahasiswa' => 'Budi Santoso',
                'email' => 'budi.santoso@student.kampus.ac.id',
                'no_hp' => '087823459900',
                'fakultas' => 'Fakultas Teknik',
                'program_studi' => 'Teknik Elektro',
                'semester' => 7,
                'layanan_id' => $layananCuti?->id,
                'jenis_layanan' => 'Pengajuan Cuti Akademik',
                'keperluan' => 'Permohonan cuti kuliah selama 1 semester karena fokus pengobatan pemulihan cedera.',
                'dokumen_lampiran' => 'surat_keterangan_dokter_rsud.pdf',
                'status' => 'menunggu',
                'catatan_petugas' => null,
                'tanggal_pengajuan' => Carbon::now()->subHours(6),
            ],
            [
                'kode_tiket' => 'PLM-202610-0004',
                'nim' => '1903010012',
                'nama_mahasiswa' => 'Dwi Lestari',
                'email' => 'dwi.lestari@alumni.kampus.ac.id',
                'no_hp' => '081398765432',
                'fakultas' => 'Fakultas Ilmu Budaya',
                'program_studi' => 'Sastra Inggris',
                'semester' => 8,
                'layanan_id' => $layananLegalisir?->id,
                'jenis_layanan' => 'Legalisir Dokumen Ijazah & Transkrip',
                'keperluan' => 'Legalisasi ijazah dan transkrip nilai 5 rangkap untuk berkas seleksi CPNS 2026.',
                'dokumen_lampiran' => 'scan_ijazah_transkrip_asli.pdf',
                'status' => 'selesai',
                'catatan_petugas' => 'Legalisir cap basah sudah selesai. Dokumen fisik dapat diambil di Loket Akademik Pusat.',
                'tanggal_pengajuan' => Carbon::now()->subDays(5),
            ],
            [
                'kode_tiket' => 'PLM-202610-0005',
                'nim' => '2404010204',
                'nama_mahasiswa' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@student.kampus.ac.id',
                'no_hp' => '089611223344',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'program_studi' => 'Sistem Informasi',
                'semester' => 1,
                'layanan_id' => $layananUkt?->id,
                'jenis_layanan' => 'Permohonan Penyesuaian / Keringanan UKT',
                'keperluan' => 'Permohonan penurunan kelompok UKT dikarenakan kepala keluarga pensiun dini.',
                'dokumen_lampiran' => 'berkas_sktm_kk_listrik.zip',
                'status' => 'diproses',
                'catatan_petugas' => 'Berkas berkas pendukung telah diterima tim verifikasi keuangan.',
                'tanggal_pengajuan' => Carbon::now()->subDays(1),
            ],
            [
                'kode_tiket' => 'PLM-202610-0006',
                'nim' => '2201010099',
                'nama_mahasiswa' => 'Maya Indrawati',
                'email' => 'maya.indra@student.kampus.ac.id',
                'no_hp' => '082155667788',
                'fakultas' => 'Fakultas Hukum',
                'program_studi' => 'Ilmu Hukum',
                'semester' => 5,
                'layanan_id' => $layananAktif?->id,
                'jenis_layanan' => 'Surat Keterangan Aktif Kuliah',
                'keperluan' => 'Pengurusan perpanjangan tunjangan asuransi kesehatan keluarga.',
                'dokumen_lampiran' => 'bukti_ukt_dan_ktm.pdf',
                'status' => 'ditolak',
                'catatan_petugas' => 'Lampiran KTM buram dan KRS semester aktif belum disahkan oleh Dosen PA. Silakan ajukan ulang dengan berkas lengkap.',
                'tanggal_pengajuan' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($samples as $sample) {
            PengajuanLayanan::updateOrCreate(['kode_tiket' => $sample['kode_tiket']], $sample);
        }
    }
}
