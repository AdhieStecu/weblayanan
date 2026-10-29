@extends('admin.layout')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Ringkasan Layanan & Antrean Approval')

@section('content')
<!-- Metric Cards Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
            📋
        </div>
        <div>
            <div class="stat-val">{{ $totalPengajuan }}</div>
            <div class="stat-lbl">Total Pengajuan</div>
        </div>
    </div>

    <div class="stat-card" style="border-color: rgba(245, 158, 11, 0.35);">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
            ⏳
        </div>
        <div>
            <div class="stat-val" style="color: #fbbf24;">{{ $menunggu }}</div>
            <div class="stat-lbl">Perlu Approval (Menunggu)</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
            ⚡
        </div>
        <div>
            <div class="stat-val" style="color: #60a5fa;">{{ $diproses }}</div>
            <div class="stat-lbl">Sedang Diproses</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
            ✓
        </div>
        <div>
            <div class="stat-val" style="color: #34d399;">{{ $selesai }}</div>
            <div class="stat-lbl">Disetujui / Selesai</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
            ✕
        </div>
        <div>
            <div class="stat-val" style="color: #f87171;">{{ $ditolak }}</div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
            👥
        </div>
        <div>
            <div class="stat-val" style="color: #818cf8;">{{ $totalMahasiswa }}</div>
            <div class="stat-lbl">Mahasiswa Terdaftar</div>
        </div>
    </div>
</div>

<!-- Section: Pengajuan Terbaru -->
<div class="content-card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <div>
            <h2 style="font-size: 1.1rem; font-weight: 800;">Antrean Pengajuan Mahasiswa Terbaru</h2>
            <p style="font-size: 0.825rem; color: var(--text-muted);">
                Tinjau dan berikan keputusan persetujuan dokumen layanan mahasiswa secara langsung.
            </p>
        </div>
        <a href="{{ route('admin.pengajuan') }}" class="btn btn-secondary btn-sm">
            Lihat Semua ({{ $totalPengajuan }}) &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tiket & Tanggal</th>
                    <th>Mahasiswa (NIM)</th>
                    <th>Program Studi</th>
                    <th>Jenis Layanan</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi Persetujuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPengajuan as $p)
                    <tr>
                        <td>
                            <strong style="font-family: var(--font-mono); color: #34d399;">{{ $p->kode_tiket }}</strong><br>
                            <span style="font-size: 0.75rem; color: var(--text-dim);">
                                {{ $p->created_at ? $p->created_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $p->nama_mahasiswa }}</div>
                            <span style="font-family: var(--font-mono); font-size: 0.78rem; color: var(--text-muted);">NIM: {{ $p->nim ?? '-' }}</span>
                        </td>
                        <td>
                            <div>{{ $p->program_studi ?? '-' }}</div>
                            <span style="font-size: 0.75rem; color: var(--text-dim);">Semester {{ $p->semester ?? '-' }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $p->jenis_layanan }}</div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $p->keperluan }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $p->status }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                                @if($p->status !== 'selesai')
                                    <!-- Approve Form Button -->
                                    <form action="{{ route('admin.pengajuan.approve', $p->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan [{{ $p->kode_tiket }}]?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Setujui Pengajuan">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                @endif

                                @if($p->status !== 'ditolak')
                                    <!-- Reject Trigger Button -->
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="openRejectModal('{{ $p->id }}', '{{ $p->kode_tiket }}', '{{ $p->nama_mahasiswa }}')"
                                        title="Tolak Pengajuan"
                                    >
                                        ✕ Tolak
                                    </button>
                                @endif

                                <a href="{{ route('admin.pengajuan', ['search' => $p->kode_tiket]) }}" class="btn btn-secondary btn-sm" title="Buka Detail">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            Belum ada permohonan pengajuan layanan dari mahasiswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Reject Pengajuan -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal-container">
        <h3 class="modal-title" style="color: #f87171;">Tolak Pengajuan Mahasiswa</h3>
        <p class="modal-desc" id="rejectModalDesc">
            Masukkan catatan atau alasan penolakan agar mahasiswa mengetahui berkas yang perlu diperbaiki.
        </p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label class="form-label" for="rejectCatatan">Alasan Penolakan / Catatan Perbaikan <span style="color: #ef4444;">*</span></label>
                <textarea
                    id="rejectCatatan"
                    name="catatan_petugas"
                    rows="3"
                    class="form-control"
                    placeholder="Contoh: Lampiran KRS belum ditandatangani Dosen Pembimbing Akademik..."
                    required
                ></textarea>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Pengajuan & Kirim Catatan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openRejectModal(id, kode, nama) {
        const form = document.getElementById('rejectForm');
        form.action = `/admin/pengajuan/${id}/reject`;
        document.getElementById('rejectModalDesc').innerText = `Menolak pengajuan [${kode}] atas nama ${nama}. Berikan alasan yang jelas.`;
        document.getElementById('rejectCatatan').value = '';
        document.getElementById('rejectModal').classList.add('active');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
    }
</script>
@endsection

