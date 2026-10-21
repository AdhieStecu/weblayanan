# Web Layanan Mahasiswa

Aplikasi portal layanan mahasiswa berbasis Fullstack menggunakan **Frontend Nuxt 4** dan **Backend Laravel API**.

---

## 📁 Struktur Direktori

```text
weblayanan/
├── beckend/               # Backend Laravel 12 (REST API & Sanctum)
│   ├── app/
│   ├── config/cors.php    # Konfigurasi CORS untuk Nuxt
│   ├── routes/api.php     # Endpoint API (/api/ping, /api/layanan)
│   └── database/          # Migrasi database SQLite
└── weblayananmahasiswa/   # Frontend Nuxt 4
    ├── app/
    │   ├── app.vue        # Tampilan antarmuka utama
    │   └── composables/   # Composable API fetcher
    └── nuxt.config.ts     # Konfigurasi runtime API
```

---

## 🚀 Panduan Menjalankan Proyek

### 1. Menjalankan Backend (Laravel)

Buka terminal dan masuk ke folder `beckend`:

```bash
cd beckend
composer install
php artisan migrate
php artisan serve
```

Server backend akan berjalan di `http://localhost:8000`.

### 2. Menjalankan Frontend (Nuxt)

Buka terminal baru dan masuk ke folder `weblayananmahasiswa`:

```bash
cd weblayananmahasiswa
npm install
npm run dev
```

Aplikasi frontend akan berjalan di `http://localhost:3000`.

---

## 🔌 Endpoint API Utama

- `GET /api/ping` : Cek status koneksi antara frontend Nuxt dan backend Laravel.
- `GET /api/layanan` : Mengambil daftar layanan administrasi dan kemahasiswaan.
- `GET /sanctum/csrf-cookie` : Inisialisasi proteksi CSRF untuk SPA.
