<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Kesehatan Sistem &bull; SiLayanan UMPKU</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #0b0f19;
            --bg-card: rgba(18, 24, 39, 0.85);
            --border: rgba(255, 255, 255, 0.08);
            --primary: #10b981;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
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
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.12) 0px, transparent 50%);
            line-height: 1.5;
        }

        .health-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            width: 100%;
            max-width: 520px;
            padding: 2.25rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(16px);
            text-align: center;
        }

        .status-badge-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
            color: #34d399;
            font-size: 2rem;
            box-shadow: 0 0 24px rgba(16, 185, 129, 0.3);
            animation: pulse-ring 2s infinite cubic-bezier(0.45, 0, 0.2, 1);
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.96); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.96); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .health-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
            color: #ffffff;
        }

        .health-sub {
            font-size: 0.875rem;
            color: #34d399;
            font-weight: 600;
            margin-bottom: 1.75rem;
        }

        .metrics-list {
            background: rgba(10, 14, 23, 0.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.75rem;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .metric-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .metric-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .metric-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .metric-val {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .tag-ok {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.4rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #34d399, #059669);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border);
            color: var(--text-main);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .json-raw-link {
            display: block;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="health-card">
        <div class="status-badge-circle">✓</div>
        <h1 class="health-title">Sistem Online & Berjalan Normal</h1>
        <p class="health-sub">Health Check Berhasil &bull; HTTP 200 OK</p>

        <div class="metrics-list">
            <div class="metric-row">
                <span class="metric-label">Status API Backend:</span>
                <span class="metric-val"><span class="tag-ok">Connected</span></span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Framework & PHP:</span>
                <span class="metric-val font-mono" style="font-size: 0.8rem;">Laravel {{ app()->version() }} / PHP {{ PHP_VERSION }}</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Koneksi Database:</span>
                <span class="metric-val"><span class="tag-ok">Aktif & Siap</span></span>
            </div>
            <div class="metric-row">
                <span class="metric-label">Waktu Server:</span>
                <span class="metric-val font-mono" style="font-size: 0.78rem;">{{ now()->toDateTimeString() }} WIB</span>
            </div>
        </div>

        <div class="btn-group">
            <a href="/" class="btn btn-secondary">&larr; Kembali ke Beranda Backend</a>
            <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}" target="_blank" class="btn btn-primary">Buka Web Mahasiswa &rarr;</a>
        </div>

        <a href="{{ url('/api/ping') }}?format=json" class="json-raw-link">
            Lihat Format JSON Mentah (Raw JSON)
        </a>
    </div>
</body>
</html>
