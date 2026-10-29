@extends('admin.layout')

@section('title', 'Master Layanan Mahasiswa')
@section('page_title', 'Master Katalog Layanan Mahasiswa (18 Layanan)')

@section('content')
<div class="content-card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.1rem; font-weight: 800;">Katalog 18 Layanan UMPKU</h2>
            <p style="font-size: 0.825rem; color: var(--text-muted);">
                Semua layanan yang tersedia di frontend mahasiswa beserta syarat berkas dan SLA estimasi hari kerja.
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Layanan</th>
                    <th>Kategori</th>
                    <th>Estimasi Waktu</th>
                    <th>Total Masuk</th>
                    <th>Status Layanan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($layanans as $index => $l)
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--text-dim);">
                            {{ $index + 1 }}
                        </td>
                        <td>
                            <strong style="color: var(--text-main);">{{ $l->nama }}</strong>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem; line-height: 1.3;">
                                {{ $l->deskripsi }}
                            </div>
                        </td>
                        <td>
                            <span class="status-badge" style="background: rgba(255, 255, 255, 0.08); color: var(--text-main);">
                                {{ $l->kategori }}
                            </span>
                        </td>
                        <td style="font-size: 0.825rem; font-weight: 600; color: #60a5fa;">
                            ⏱️ {{ $l->estimasi ?? '1-3 Hari Kerja' }}
                        </td>
                        <td>
                            <span style="font-weight: 700; font-family: var(--font-mono);">
                                {{ $l->pengajuan_count ?? 0 }}
                            </span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">berkas</span>
                        </td>
                        <td>
                            @if($l->status === 'aktif')
                                <span class="status-badge status-selesai">Aktif</span>
                            @else
                                <span class="status-badge status-ditolak">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.layanan.toggle', $l->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn btn-sm {{ $l->status === 'aktif' ? 'btn-secondary' : 'btn-success' }}"
                                    title="Ubah Status Aktif/Nonaktif"
                                >
                                    {{ $l->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            Belum ada master layanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

