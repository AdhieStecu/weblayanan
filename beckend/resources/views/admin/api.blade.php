@extends('admin.layout')

@section('title', 'Dokumentasi REST API')
@section('page_title', 'Dokumentasi & Integrasi REST API Backend')

@section('content')
<div class="content-card" style="max-width: 900px;">
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800;">Panduan Integrasi REST API SiLayanan UMPKU</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">
            Seluruh endpoint REST API di bawah dapat diakses oleh client frontend maupun tools pengujian seperti Postman, Insomnia, dan curl.
        </p>
    </div>

    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.825rem; color: #34d399; font-weight: 700; margin-bottom: 0.25rem;">
            Base URL API:
        </div>
        <code style="font-family: var(--font-mono); font-size: 0.95rem; color: #ffffff;">
            http://127.0.0.1:8000/api
        </code>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Metode</th>
                    <th>Endpoint Path</th>
                    <th>Hak Akses</th>
                    <th>Fungsi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="status-badge status-diproses">GET</span></td>
                    <td><code>/api/ping</code></td>
                    <td>Publik</td>
                    <td>Health check koneksi backend & status server</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-diproses">GET</span></td>
                    <td><code>/api/layanan</code></td>
                    <td>Publik</td>
                    <td>Mengambil daftar 18 master layanan mahasiswa</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-diproses">GET</span></td>
                    <td><code>/api/cms/content</code></td>
                    <td>Publik</td>
                    <td>Mengambil data hero banner & pengumuman landing</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-selesai">POST</span></td>
                    <td><code>/api/auth/register</code></td>
                    <td>Publik</td>
                    <td>Registrasi akun mahasiswa baru</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-selesai">POST</span></td>
                    <td><code>/api/auth/login</code></td>
                    <td>Publik</td>
                    <td>Autentikasi akun & mendapatkan Sanctum Bearer Token</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-diproses">GET</span></td>
                    <td><code>/api/pengajuan</code></td>
                    <td>Bearer Token</td>
                    <td>Melihat riwayat pengajuan (filter NIM / semua)</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-selesai">POST</span></td>
                    <td><code>/api/pengajuan</code></td>
                    <td>Bearer Token</td>
                    <td>Membuat pengajuan layanan mahasiswa baru</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-selesai">POST</span></td>
                    <td><code>/api/pengajuan/{id}/approve</code></td>
                    <td>Admin Only</td>
                    <td>Menyetujui permohonan pengajuan layanan</td>
                </tr>
                <tr>
                    <td><span class="status-badge status-selesai">POST</span></td>
                    <td><code>/api/pengajuan/{id}/reject</code></td>
                    <td>Admin Only</td>
                    <td>Menolak pengajuan layanan dengan catatan penolakan</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

