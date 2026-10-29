<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator &bull; SiLayanan UMPKU</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #0a0e17;
            --bg-card: rgba(18, 24, 39, 0.9);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #10b981;
            --primary-hover: #059669;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
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
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.12) 0px, transparent 50%);
            padding: 1.5rem;
            line-height: 1.5;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(16px);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .login-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(10, 14, 23, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem 1rem;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #34d399, #059669);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .hint-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-top: 1.75rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .hint-box code {
            font-family: var(--font-mono);
            color: #34d399;
            background: rgba(0, 0, 0, 0.3);
            padding: 0.15rem 0.35rem;
            border-radius: 4px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.825rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <div class="brand-pill">
                <span>🛡️ Portal Khusus Pengelola</span>
            </div>
            <h1 class="login-title">Administrator Login</h1>
            <p class="login-desc">Sistem Informasi Layanan Mahasiswa UMPKU</p>
        </div>

        @if(session('info'))
            <div class="alert-info">
                {{ session('info') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert-danger">
                {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger">
                <ul style="padding-left: 1rem; margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email atau Username Admin</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    value="{{ old('email', 'admin@umpku.ac.id') }}"
                    placeholder="admin@umpku.ac.id"
                    class="form-control"
                    required
                    autofocus
                />
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    value="admin123"
                    placeholder="••••••••"
                    class="form-control"
                    required
                />
            </div>

            <button type="submit" class="btn-submit">
                Masuk ke Panel Admin &rarr;
            </button>
        </form>

        <div class="hint-box">
            <strong>Kredensial Default Administrator:</strong><br>
            Email: <code>admin@umpku.ac.id</code><br>
            Password: <code>admin123</code>
        </div>

        <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}" class="back-link">
            &larr; Buka Portal Mahasiswa (Nuxt Frontend)
        </a>
    </div>
</body>
</html>

