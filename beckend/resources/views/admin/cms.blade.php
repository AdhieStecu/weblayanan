@extends('admin.layout')

@section('title', 'CMS Banner & Konten')
@section('page_title', 'CMS Banner Hero & Pengumuman Landing Page')

@section('content')
<div class="content-card" style="max-width: 820px;">
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800;">Pengaturan Konten Hero Banner</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">
            Teks dan banner yang diatur di sini akan langsung ditampilkan pada halaman beranda utama mahasiswa di <code>http://localhost:3000</code>.
        </p>
    </div>

    <form action="{{ route('admin.cms.update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="hero_title">Judul Utama Hero Banner (Title) <span style="color: #ef4444;">*</span></label>
            <input
                type="text"
                id="hero_title"
                name="hero_title"
                value="{{ old('hero_title', $landingContent['hero_title']->value ?? 'Layanan Mahasiswa UMPKU') }}"
                class="form-control"
                placeholder="Contoh: Layanan Mahasiswa UMPKU"
                required
            />
        </div>

        <div class="form-group">
            <label class="form-label" for="hero_subtitle">Subjudul / Deskripsi Singkat <span style="color: #ef4444;">*</span></label>
            <textarea
                id="hero_subtitle"
                name="hero_subtitle"
                rows="3"
                class="form-control"
                placeholder="Portal Layanan Terpadu Administrasi, Riset & Akademik Mahasiswa Universitas Muhammadiyah Kudus"
                required
            >{{ old('hero_subtitle', $landingContent['hero_subtitle']->value ?? 'Portal Layanan Terpadu Administrasi, Riset & Akademik Mahasiswa Universitas Muhammadiyah Kudus') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="hero_announcement">Teks Informasi / Pengumuman Penting</label>
            <input
                type="text"
                id="hero_announcement"
                name="hero_announcement"
                value="{{ old('hero_announcement', $landingContent['hero_announcement']->value ?? 'Sebelum mengisi formulir pengajuan, pastikan sudah login menggunakan akun email mahasiswa Anda.') }}"
                class="form-control"
                placeholder="Pengumuman berkala untuk mahasiswa..."
            />
        </div>

        <div class="form-group">
            <label class="form-label" for="hero_banner_image">URL Gambar Background Hero Banner</label>
            <input
                type="text"
                id="hero_banner_image"
                name="hero_banner_image"
                value="{{ old('hero_banner_image', $landingContent['hero_banner_image']->value ?? 'http://127.0.0.1:8000/uploads/landing/hero-banner-default-1792656714.jpg') }}"
                class="form-control font-mono font-sm"
                placeholder="http://127.0.0.1:8000/uploads/..."
            />
        </div>

        <div style="margin-top: 1.75rem;">
            <button type="submit" class="btn btn-primary">
                ✓ Simpan Perubahan CMS
            </button>
            <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}" target="_blank" class="btn btn-secondary" style="margin-left: 0.5rem;">
                Lihat di Web Mahasiswa &rarr;
            </a>
        </div>
    </form>
</div>
@endsection

