@extends('admin.layout')

@section('title', 'Manajemen Pengajuan')
@section('page_title', 'Kelola Pengajuan Layanan Mahasiswa')

@section('content')
<!-- Header Stats Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <a href="{{ route('admin.pengajuan', ['status' => 'semua']) }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="{{ request('status', 'semua') === 'semua' ? 'border-color: var(--primary);' : '' }}">
            <div>
                <div class="stat-val">{{ $stats['total'] }}</div>
                <div class="stat-lbl">Semua Pengajuan</div>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.pengajuan', ['status' => 'menunggu']) }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-color: rgba(245, 158, 11, 0.4); {{ request('status') === 'menunggu' ? 'background: rgba(245, 158, 11, 0.1);' : '' }}">
            <div>
                <div class="stat-val" style="color: #fbbf24;">{{ $stats['menunggu'] }}</div>
                <div class="stat-lbl">Menunggu Review</div>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.pengajuan', ['status' => 'diproses']) }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="{{ request('status') === 'diproses' ? 'border-color: var(--info);' : '' }}">
            <div>
                <div class="stat-val" style="color: #60a5fa;">{{ $stats['diproses'] }}</div>
                <div class="stat-lbl">Sedang Diproses</div>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.pengajuan', ['status' => 'selesai']) }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="{{ request('status') === 'selesai' ? 'border-color: var(--primary);' : '' }}">
            <div>
                <div class="stat-val" style="color: #34d399;">{{ $stats['selesai'] }}</div>
                <div class="stat-lbl">Disetujui / Selesai</div>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.pengajuan', ['status' => 'ditolak']) }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="{{ request('status') === 'ditolak' ? 'border-color: var(--danger);' : '' }}">
            <div>
                <div class="stat-val" style="color: #f87171;">{{ $stats['ditolak'] }}</div>
                <div class="stat-lbl">Ditolak</div>
            </div>
        </div>
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="content-card" style="padding: 1.25rem;">
    <form action="{{ route('admin.pengajuan') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <div style="flex: 2; min-width: 240px;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari berdasarkan Kode Tiket, NIM, Nama Mahasiswa..."
                class="form-control"
            />
        </div>

        <div style="flex: 1; min-width: 180px;">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Filter Status: Semua</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>🟡 Menunggu Review</option>
                <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>🔵 Sedang Diproses</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>🟢 Disetujui / Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>🔴 Ditolak</option>
            </select>
        </div>

        <div style="flex: 1.5; min-width: 200px;">
            <select name="layanan_id" class="form-control" onchange="this.form.submit()">
                <option value="semua">Filter Layanan: Semua Katalog</option>
                @foreach($layanans as $l)
                    <option value="{{ $l->id }}" {{ request('layanan_id') == $l->id ? 'selected' : '' }}>
                        {{ $l->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Cari
            </button>
            @if(request()->hasAny(['search', 'status', 'layanan_id']))
                <a href="{{ route('admin.pengajuan') }}" class="btn btn-secondary">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table of Submissions -->
<div class="content-card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.1rem; font-weight: 800;">
            Daftar Pengajuan ({{ $pengajuans->total() }} Data)
        </h2>
        <span style="font-size: 0.8rem; color: var(--text-muted);">
            Klik Setujui untuk menyetujui langsung atau Tolak dengan catatan.
        </span>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tiket & Tanggal</th>
                    <th>Mahasiswa</th>
                    <th>Layanan & Keperluan</th>
                    <th>Lampiran</th>
                    <th>Status & Catatan</th>
                    <th style="text-align: right;">Aksi Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $p)
                    <tr>
                        <td>
                            <strong style="font-family: var(--font-mono); color: #34d399; font-size: 0.9rem;">
                                {{ $p->kode_tiket }}
                            </strong>
                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.2rem;">
                                {{ $p->created_at ? $p->created_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $p->nama_mahasiswa }}</div>
                            <div style="font-family: var(--font-mono); font-size: 0.78rem; color: var(--text-muted);">
                                NIM: {{ $p->nim ?? '-' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-dim);">
                                {{ $p->program_studi ?? '-' }} (Smt {{ $p->semester ?? '-' }})
                            </div>
                            @if($p->no_hp)
                                <div style="font-size: 0.73rem; color: var(--primary);">
                                    📱 {{ $p->no_hp }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #e2e8f0;">{{ $p->jenis_layanan }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; max-width: 260px; line-height: 1.4;">
                                {{ $p->keperluan }}
                            </div>
                        </td>
                        <td>
                            @if($p->dokumen_lampiran)
                                <span style="font-family: var(--font-mono); font-size: 0.75rem; color: #60a5fa; background: rgba(59, 130, 246, 0.1); padding: 0.2rem 0.4rem; border-radius: 4px; display: inline-block;">
                                    📎 {{ $p->dokumen_lampiran }}
                                </span>
                            @else
                                <span style="font-size: 0.75rem; color: var(--text-dim);">- Tidak ada berkas -</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $p->status }}">
                                {{ ucfirst($p->status) }}
                            </span>
                            @if($p->catatan_petugas)
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; max-width: 200px; font-style: italic;">
                                    "{{ $p->catatan_petugas }}"
                                </div>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem; flex-wrap: wrap;">
                                @if($p->status !== 'selesai')
                                    <!-- One-click Approve -->
                                    <form action="{{ route('admin.pengajuan.approve', $p->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Setujui resmi pengajuan [{{ $p->kode_tiket }}] untuk {{ $p->nama_mahasiswa }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Setujui Pengajuan">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                @endif

                                @if($p->status !== 'ditolak')
                                    <!-- Reject with Reason Modal -->
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="openRejectModal('{{ $p->id }}', '{{ $p->kode_tiket }}', '{{ $p->nama_mahasiswa }}')"
                                        title="Tolak Pengajuan"
                                    >
                                        ✕ Tolak
                                    </button>
                                @endif

                                <!-- Quick Status Dropdown Modal -->
                                <button
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    onclick="openStatusModal('{{ $p->id }}', '{{ $p->kode_tiket }}', '{{ $p->status }}', '{{ addslashes($p->catatan_petugas ?? '') }}')"
                                    title="Ubah Status / Catatan"
                                >
                                    ⚡ Status
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('admin.pengajuan.delete', $p->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus permanen pengajuan [{{ $p->kode_tiket }}]? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: #f87171;" title="Hapus Data">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            Tidak ada data pengajuan yang cocok dengan kriteria pencarian / filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
        {{ $pengajuans->links() }}
    </div>
</div>

<!-- Modal 1: Tolak Pengajuan -->
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
                    placeholder="Contoh: Lampiran berkas belum lengkap, mohon upload ulang bukti pembayaran UKT..."
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

<!-- Modal 2: Ubah Status & Catatan -->
<div class="modal-overlay" id="statusModal">
    <div class="modal-container">
        <h3 class="modal-title">Perbarui Status & Catatan Petugas</h3>
        <p class="modal-desc" id="statusModalDesc">
            Sesuaikan tahapan proses dokumen pengajuan mahasiswa.
        </p>

        <form id="statusForm" method="POST" action="">
            @csrf
            <div class="form-group">
                <label class="form-label">Status Baru</label>
                <select name="status" id="statusSelect" class="form-control">
                    <option value="menunggu">🟡 Menunggu Review</option>
                    <option value="diproses">🔵 Sedang Diproses</option>
                    <option value="selesai">🟢 Disetujui / Selesai</option>
                    <option value="ditolak">🔴 Ditolak</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Resmi Petugas (Tampil di Mahasiswa)</label>
                <textarea
                    name="catatan_petugas"
                    id="statusCatatan"
                    rows="3"
                    class="form-control"
                    placeholder="Catatan progres atau nomor surat resmi..."
                ></textarea>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Status</button>
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
        document.getElementById('rejectModalDesc').innerText = `Menolak pengajuan [${kode}] atas nama ${nama}. Berikan catatan yang jelas.`;
        document.getElementById('rejectCatatan').value = '';
        document.getElementById('rejectModal').classList.add('active');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
    }

    function openStatusModal(id, kode, currentStatus, currentNotes) {
        const form = document.getElementById('statusForm');
        form.action = `/admin/pengajuan/${id}/status`;
        document.getElementById('statusModalDesc').innerText = `Ubah status tiket [${kode}].`;
        document.getElementById('statusSelect').value = currentStatus;
        document.getElementById('statusCatatan').value = currentNotes;
        document.getElementById('statusModal').classList.add('active');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.remove('active');
    }
</script>
@endsection

