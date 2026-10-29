<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backend API &bull; SiLayanan Mahasiswa UMPKU</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #0b0f19;
            --bg-card: rgba(18, 24, 39, 0.75);
            --bg-card-hover: rgba(28, 38, 59, 0.85);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-accent: rgba(16, 185, 129, 0.35);
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --emerald-glow: #10b981;
            --emerald-bg: rgba(16, 185, 129, 0.12);
            --indigo-accent: #6366f1;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-main);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(30, 41, 59, 0.25) 0px, transparent 50%);
            background-attachment: fixed;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            width: 100%;
        }

        /* Header Bar */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .brand-pill {
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
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }

        .brand-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--emerald-bg);
            border: 1px solid var(--border-accent);
            color: #34d399;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 600;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--emerald-glow);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2s infinite cubic-bezier(0.45, 0, 0.2, 1);
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Hero / Callout */
        .hero-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 2.25rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
        }

        .hero-title {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 680px;
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }

        .cta-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            text-decoration: none;
            padding: 0.85rem 1.75rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
            background: linear-gradient(135deg, #34d399, #059669);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            text-decoration: none;
            padding: 0.85rem 1.4rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Stats Grid */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1.25rem;
            backdrop-filter: blur(8px);
        }

        .metric-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .metric-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-tag {
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .status-tag.ok {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .status-tag.notice {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
        }

        /* Endpoints Table */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 1.75rem;
            margin-bottom: 2.5rem;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .table-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }

        th {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #d1d5db;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge-method {
            font-family: var(--font-mono);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.45rem;
            border-radius: 4px;
            display: inline-block;
        }

        .badge-get {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge-post {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-patch {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .endpoint-code {
            font-family: var(--font-mono);
            font-size: 0.825rem;
            color: #e5e7eb;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        .link-test {
            color: #60a5fa;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .link-test:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.825rem;
            color: var(--text-muted);
        }

        @media (max-width: 640px) {
            .hero-title {
                font-size: 1.45rem;
            }
            .top-nav {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .cta-group {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-primary, .btn-secondary {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top Navigation -->
        <header class="top-nav">
            <div class="brand-pill">
                <span class="brand-badge">UMPKU</span>
                <span class="brand-title">SiLayanan Mahasiswa Backend</span>
            </div>
            <div class="status-pill">
                <span class="pulse-dot"></span>
                <span>API Server Active &bull; Port 8000</span>
            </div>
        </header>

        <!-- Hero Notice Card -->
        <section class="hero-card">
            <h1 class="hero-title">Backend API SiLayanan Mahasiswa UMPKU</h1>
            <p class="hero-desc">
                Server backend Laravel berjalan normal dan siap melayani permintaan API. Aplikasi antarmuka pengguna (Frontend Mahasiswa & Admin) berjalan di <strong>Nuxt 4 pada port 3000</strong>.
            </p>
            <div class="cta-group">
                <a href="{{ route('admin.login') }}" class="btn-primary" style="background: linear-gradient(135deg, #059669, #10b981);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    🔐 Masuk ke Portal Administrator (Backend) &rarr;
                </a>
                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}" class="btn-secondary" target="_blank" rel="noopener">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Buka Web Mahasiswa (Frontend Nuxt)
                </a>
                <button type="button" class="btn-secondary" onclick="checkHealthModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Test Health Check
                </button>
                <a href="{{ url('/api/ping') }}" class="btn-secondary" target="_blank" style="font-size: 0.8rem; padding: 0.85rem 1rem;">
                    Halaman Status (/api/ping)
                </a>
            </div>
        </section>

        <!-- Metric Cards -->
        <section class="grid-3">
            <div class="metric-card">
                <div class="metric-label">Status Server</div>
                <div class="metric-value">
                    <span>Laravel {{ app()->version() }}</span>
                    <span class="status-tag ok">PHP {{ PHP_VERSION }}</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Database MySQL</div>
                <div class="metric-value">
                    @if(isset($dbStatus) && $dbStatus)
                        <span>Terkoneksi</span>
                        <span class="status-tag ok">MAMP MySQL</span>
                    @else
                        <span>Siap Digunakan</span>
                        <span class="status-tag ok">Active</span>
                    @endif
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Frontend URL</div>
                <div class="metric-value">
                    <span style="font-size: 0.95rem; font-family: var(--font-mono);">{{ config('app.frontend_url', 'http://localhost:3000') }}</span>
                </div>
            </div>
        </section>

        <!-- Endpoints List -->
        <section class="table-card">
            <div class="table-header">
                <h2 class="table-title">Daftar Endpoint REST API</h2>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Format: JSON Response</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 90px;">Metode</th>
                            <th>Endpoint</th>
                            <th>Keterangan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge-method badge-get">GET</span></td>
                            <td><code class="endpoint-code">/api/ping</code></td>
                            <td>Health check koneksi API Backend &bull; Frontend</td>
                            <td style="text-align: right;"><a href="{{ url('/api/ping') }}" target="_blank" class="link-test">Buka &rarr;</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-get">GET</span></td>
                            <td><code class="endpoint-code">/api/layanan</code></td>
                            <td>Daftar 18 katalog layanan mahasiswa UMPKU</td>
                            <td style="text-align: right;"><a href="{{ url('/api/layanan') }}" target="_blank" class="link-test">Buka &rarr;</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-get">GET</span></td>
                            <td><code class="endpoint-code">/api/cms/content</code></td>
                            <td>Pengaturan banner hero & konten landing page</td>
                            <td style="text-align: right;"><a href="{{ url('/api/cms/content') }}" target="_blank" class="link-test">Buka &rarr;</a></td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-post">POST</span></td>
                            <td><code class="endpoint-code">/api/auth/login</code></td>
                            <td>Autentikasi akun pengguna (Admin / Mahasiswa)</td>
                            <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">Auth</td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-post">POST</span></td>
                            <td><code class="endpoint-code">/api/auth/register</code></td>
                            <td>Registrasi mahasiswa baru dengan pilihan role</td>
                            <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">Auth</td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-get">GET</span></td>
                            <td><code class="endpoint-code">/api/pengajuan</code></td>
                            <td>Data pengajuan layanan (Filter user / admin)</td>
                            <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">Bearer Token</td>
                        </tr>
                        <tr>
                            <td><span class="badge-method badge-post">POST</span></td>
                            <td><code class="endpoint-code">/api/pengajuan/{id}/approve</code></td>
                            <td>Persetujuan pengajuan layanan oleh Admin</td>
                            <td style="text-align: right; color: var(--text-muted); font-size: 0.8rem;">Admin Only</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <footer>
        <div class="container" style="padding-top: 0; padding-bottom: 0;">
            &copy; {{ date('Y') }} Universitas Muhammadiyah Kudus (UMPKU) &bull; Sistem Informasi Layanan Mahasiswa Terpadu
        </div>
    </footer>
    <!-- Health Check Modal -->
    <div id="healthModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 100; padding: 1rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.6); text-align: center;">
            <div id="healthSpinner" style="display: block; font-size: 1.5rem; margin-bottom: 1rem;">⏳ Memeriksa status sistem...</div>
            <div id="healthContent" style="display: none;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(16,185,129,0.15); border: 2px solid #10b981; color: #34d399; font-size: 1.6rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">✓</div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-bottom: 0.25rem;">Sistem 100% Online & Sehat!</h3>
                <p id="healthMsg" style="font-size: 0.85rem; color: #9ca3af; margin-bottom: 1.25rem; line-height: 1.4;"></p>
                <div style="background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.75rem; text-align: left; font-family: var(--font-mono); font-size: 0.78rem; color: #d1d5db; margin-bottom: 1.5rem;">
                    <div>Status HTTP: <strong style="color: #34d399;">200 OK</strong></div>
                    <div id="healthTime" style="margin-top: 0.25rem;"></div>
                </div>
            </div>
            <button type="button" class="btn-primary" style="width: 100%; justify-content: center;" onclick="document.getElementById('healthModal').style.display='none'">
                Tutup Jendela
            </button>
        </div>
    </div>

    <script>
        async function checkHealthModal() {
            const modal = document.getElementById('healthModal');
            const spinner = document.getElementById('healthSpinner');
            const content = document.getElementById('healthContent');
            const msg = document.getElementById('healthMsg');
            const time = document.getElementById('healthTime');

            modal.style.display = 'flex';
            spinner.style.display = 'block';
            content.style.display = 'none';

            try {
                const res = await fetch('/api/ping?format=json');
                const data = await res.json();
                spinner.style.display = 'none';
                content.style.display = 'block';
                msg.textContent = data.message || 'API Backend Laravel berhasil terhubung dan siap melayani permintaan!';
                time.textContent = 'Server Time: ' + (data.server_time || new Date().toISOString());
            } catch (e) {
                spinner.style.display = 'none';
                content.style.display = 'block';
                msg.textContent = 'Gagal menghubungi server: ' + e.message;
            }
        }
    </script>
</body>
</html>

