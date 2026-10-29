@extends('admin.layout')

@section('title', 'Daftar Mahasiswa & Pengguna')
@section('page_title', 'Manajemen Pengguna & Mahasiswa')

@section('content')
<!-- Stats Summary -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div>
            <div class="stat-val">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total Seluruh Akun</div>
        </div>
    </div>

    <div class="stat-card" style="border-color: rgba(99, 102, 241, 0.4);">
        <div>
            <div class="stat-val" style="color: #818cf8;">{{ $stats['mahasiswa'] }}</div>
            <div class="stat-lbl">Mahasiswa Terdaftar</div>
        </div>
    </div>

    <div class="stat-card" style="border-color: rgba(16, 185, 129, 0.4);">
        <div>
            <div class="stat-val" style="color: #34d399;">{{ $stats['admin'] }}</div>
            <div class="stat-lbl">Staf Administrator</div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="content-card" style="padding: 1.25rem;">
    <form action="{{ route('admin.users') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 240px;">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari berdasarkan Nama, Email, NIM, atau Program Studi..."
                class="form-control"
            />
        </div>

        <div style="flex: 1; min-width: 160px;">
            <select name="role" class="form-control" onchange="this.form.submit()">
                <option value="semua" {{ request('role', 'semua') === 'semua' ? 'selected' : '' }}>Semua Peran</option>
                <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>🎓 Mahasiswa</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>🛡️ Administrator</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="content-card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.1rem; font-weight: 800;">
            Daftar Pengguna Sistem ({{ $users->total() }} Akun)
        </h2>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pengguna / Nama</th>
                    <th>NIM & Akademik</th>
                    <th>Kontak (Email / HP)</th>
                    <th>Peran</th>
                    <th>Tanggal Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $u->name }}</div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">ID: #{{ $u->id }}</span>
                        </td>
                        <td>
                            @if($u->nim)
                                <strong style="font-family: var(--font-mono); color: #34d399;">{{ $u->nim }}</strong>
                                <div style="font-size: 0.75rem; color: var(--text-dim);">
                                    {{ $u->program_studi ?? '-' }} (Smt {{ $u->semester ?? '-' }})
                                </div>
                            @else
                                <span style="font-size: 0.78rem; color: var(--text-dim);">- Administrator Sistem -</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $u->email }}</div>
                            @if($u->no_hp)
                                <div style="font-size: 0.75rem; color: var(--primary);">📱 {{ $u->no_hp }}</div>
                            @endif
                        </td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="status-badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);">
                                    🛡️ Administrator
                                </span>
                            @else
                                <span class="status-badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.4);">
                                    🎓 Mahasiswa
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">
                            {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            Tidak ada akun pengguna yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
        {{ $users->links() }}
    </div>
</div>
@endsection

