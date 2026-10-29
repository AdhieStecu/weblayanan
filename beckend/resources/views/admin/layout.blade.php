<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') &bull; SiLayanan UMPKU</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #0a0e17;
            --bg-sidebar: #0f1626;
            --bg-card: #151e33;
            --bg-card-hover: #1c2844;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-focus: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            --primary: #10b981;
            --primary-hover: #059669;
            --primary-glow: rgba(16, 185, 129, 0.2);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.15);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.15);
            --info: #3b82f6;
            --info-bg: rgba(59, 130, 246, 0.15);
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            transition: transform 0.25s ease;
        }

        .sidebar-header {
            padding: 1.25rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-badge {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            font-weight: 800;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
        }

        .brand-text h2 {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
        }

        .brand-text span {
            font-size: 0.7rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .nav-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-dim);
            padding: 0.75rem 0.75rem 0.35rem 0.75rem;
            font-weight: 700;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .nav-link:hover {
            color: var(--text-main);
            background: var(--bg-card);
        }

        .nav-link.active {
            color: #ffffff;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .nav-link-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-icon {
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
        }

        .nav-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            background: rgba(10, 14, 23, 0.5);
        }

        .admin-profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .profile-name {
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-role {
            font-size: 0.7rem;
            color: var(--primary);
            font-weight: 600;
        }

        /* Main Content Layout */
        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        .admin-topbar {
            height: 64px;
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-view-client {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view-client:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--primary);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.825rem;
            font-weight: 600;
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .admin-content {
            padding: 2rem 1.75rem;
            flex: 1;
        }

        /* Cards, Tables & Buttons */
        .content-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.75rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-val {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-lbl {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Status Pills */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            text-transform: capitalize;
        }

        .status-menunggu {
            background: var(--warning-bg);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-diproses {
            background: var(--info-bg);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-selesai {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-ditolak {
            background: var(--danger-bg);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-success {
            background: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background: #10b981;
        }

        .btn-danger {
            background: #dc2626;
            color: #ffffff;
        }
        .btn-danger:hover {
            background: #ef4444;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 0.78rem;
            border-radius: 6px;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table.admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }

        table.admin-table th {
            padding: 0.85rem 1rem;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            background: rgba(15, 22, 38, 0.6);
            font-weight: 700;
        }

        table.admin-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            vertical-align: middle;
        }

        table.admin-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Alerts */
        .alert {
            padding: 0.9rem 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            padding: 1.75rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .modal-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.9rem;
            background: rgba(10, 14, 23, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        @media (max-width: 900px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar Navigation (Backend Admin Only) -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <span class="brand-badge">UMPKU</span>
            <div class="brand-text">
                <h2>SiLayanan Admin</h2>
                <span>Portal Pengelola</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-category">Navigasi Utama</div>

            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard Utama</span>
                </div>
            </a>

            <a href="{{ route('admin.pengajuan') }}" class="nav-link {{ request()->routeIs('admin.pengajuan*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">📋</span>
                    <span>Semua Pengajuan</span>
                </div>
                @php
                    $pendingCount = \App\Models\PengajuanLayanan::where('status', 'menunggu')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="nav-badge">{{ $pendingCount }} Baru</span>
                @endif
            </a>

            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">👥</span>
                    <span>Daftar Mahasiswa</span>
                </div>
            </a>

            <div class="nav-category">Konfigurasi & Master</div>

            <a href="{{ route('admin.layanan') }}" class="nav-link {{ request()->routeIs('admin.layanan*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">📑</span>
                    <span>Master 18 Layanan</span>
                </div>
            </a>

            <a href="{{ route('admin.cms') }}" class="nav-link {{ request()->routeIs('admin.cms*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">🖼️</span>
                    <span>Banner Hero & CMS</span>
                </div>
            </a>

            <a href="{{ route('admin.api') }}" class="nav-link {{ request()->routeIs('admin.api*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <span class="nav-icon">🔌</span>
                    <span>Dokumentasi API</span>
                </div>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="profile-info">
                    <span class="profile-name">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <span class="profile-role">🛡️ Admin Terverifikasi</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="btn btn-secondary btn-sm" id="sidebarToggle" style="display: none;">
                    ☰
                </button>
                <h1 class="page-header-title">@yield('page_title', 'Dashboard Administrator')</h1>
            </div>

            <div class="topbar-right">
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}" target="_blank" class="btn-view-client" title="Buka Aplikasi Web Mahasiswa">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Buka Web Mahasiswa &rarr;</span>
                </a>
            </div>
        </header>

        <div class="admin-content">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <span>⚠️</span>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <span>ℹ️</span>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <span>✕</span>
                    <div>
                        <ul style="padding-left: 1rem; margin: 0;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>

