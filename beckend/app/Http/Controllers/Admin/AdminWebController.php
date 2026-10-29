<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use App\Models\Layanan;
use App\Models\PengajuanLayanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminWebController extends Controller
{
    /**
     * Memastikan minimal satu akun Administrator siap digunakan di database.
     */
    protected function ensureDefaultAdminExists(): ?User
    {
        try {
            $admin = User::firstOrNew(['email' => 'admin@umpku.ac.id']);
            if (! $admin->exists) {
                $admin->name = 'Administrator Akademik UMPKU';
                $admin->password = Hash::make('admin123');
                $admin->role = 'admin';
                $admin->program_studi = 'Biro Administrasi Akademik';
                $admin->no_hp = '081234567890';
                $admin->save();
            }

            return $admin;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Tampilkan halaman login Admin.
     */
    public function loginView(): View|RedirectResponse
    {
        $this->ensureDefaultAdminExists();

        if (session('admin_logged_in') && Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Proses autentikasi akun Administrator.
     */
    public function login(Request $request): RedirectResponse
    {
        $this->ensureDefaultAdminExists();

        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau username administrator wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', trim($credentials['email']))
            ->orWhere('nim', trim($credentials['email']))
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Kredensial login administrator tidak cocok dengan data kami.']);
        }

        if ($user->role !== 'admin') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akses ditolak: Akun ini terdaftar sebagai Mahasiswa, bukan Administrator.']);
        }

        Auth::login($user, (bool) $request->boolean('remember'));
        session(['admin_logged_in' => true, 'admin_user_id' => $user->id]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang di Portal Administrator UMPKU, ' . $user->name . '!');
    }

    /**
     * Logout Administrator.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(['admin_logged_in', 'admin_user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('info', 'Anda telah berhasil logout dari Portal Administrator.');
    }

    /**
     * Dashboard Utama Administrator.
     */
    public function dashboard(): View
    {
        $totalPengajuan = PengajuanLayanan::count();
        $menunggu = PengajuanLayanan::where('status', 'menunggu')->count();
        $diproses = PengajuanLayanan::where('status', 'diproses')->count();
        $selesai = PengajuanLayanan::where('status', 'selesai')->count();
        $ditolak = PengajuanLayanan::where('status', 'ditolak')->count();
        $totalMahasiswa = User::where('role', '!=', 'admin')->count();

        // 6 Pengajuan terbaru yang membutuhkan tindakan
        $recentPengajuan = PengajuanLayanan::with('layanan')
            ->orderByRaw("FIELD(status, 'menunggu', 'diproses', 'ditolak', 'selesai')")
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengajuan',
            'menunggu',
            'diproses',
            'selesai',
            'ditolak',
            'totalMahasiswa',
            'recentPengajuan'
        ));
    }

    /**
     * Manajemen Semua Pengajuan Mahasiswa (Approval / Reject / Search / Filter).
     */
    public function pengajuan(Request $request): View
    {
        $query = PengajuanLayanan::with('layanan');

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter jenis layanan
        if ($request->filled('layanan_id') && $request->layanan_id !== 'semua') {
            $query->where('layanan_id', $request->layanan_id);
        }

        // Pencarian (NIM, Nama, Kode Tiket, Program Studi)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $pengajuans = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $layanans = Layanan::orderBy('nama')->get();

        $stats = [
            'total' => PengajuanLayanan::count(),
            'menunggu' => PengajuanLayanan::where('status', 'menunggu')->count(),
            'diproses' => PengajuanLayanan::where('status', 'diproses')->count(),
            'selesai' => PengajuanLayanan::where('status', 'selesai')->count(),
            'ditolak' => PengajuanLayanan::where('status', 'ditolak')->count(),
        ];

        return view('admin.pengajuan', compact('pengajuans', 'layanans', 'stats'));
    }

    /**
     * Approve / Setujui Pengajuan Mahasiswa.
     */
    public function approvePengajuan(Request $request, int $id): RedirectResponse
    {
        $pengajuan = PengajuanLayanan::findOrFail($id);

        $catatan = $request->input(
            'catatan_petugas',
            'Pengajuan telah diverifikasi dan disetujui resmi oleh Administrator Akademik UMPKU.'
        );

        $pengajuan->update([
            'status' => 'selesai',
            'catatan_petugas' => $catatan,
        ]);

        return back()->with('success', "Pengajuan tiket [{$pengajuan->kode_tiket}] milik {$pengajuan->nama_mahasiswa} berhasil DISETUJUI (Approved)!");
    }

    /**
     * Reject / Tolak Pengajuan Mahasiswa dengan Catatan.
     */
    public function rejectPengajuan(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'catatan_petugas' => ['required', 'string', 'min:5'],
        ], [
            'catatan_petugas.required' => 'Alasan penolakan pengajuan wajib diisi agar mahasiswa dapat memperbaiki.',
            'catatan_petugas.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $pengajuan = PengajuanLayanan::findOrFail($id);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_petugas' => $request->input('catatan_petugas'),
        ]);

        return back()->with('warning', "Pengajuan tiket [{$pengajuan->kode_tiket}] ditolak. Catatan penolakan telah tersimpan.");
    }

    /**
     * Update Status Pengajuan Langsung (Menunggu / Diproses / Selesai / Ditolak).
     */
    public function updateStatusPengajuan(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai,ditolak'],
            'catatan_petugas' => ['nullable', 'string'],
        ]);

        $pengajuan = PengajuanLayanan::findOrFail($id);

        $pengajuan->update([
            'status' => $request->status,
            'catatan_petugas' => $request->catatan_petugas ?: $pengajuan->catatan_petugas,
        ]);

        return back()->with('success', "Status pengajuan [{$pengajuan->kode_tiket}] berhasil diubah menjadi: " . strtoupper($request->status));
    }

    /**
     * Hapus Pengajuan Mahasiswa.
     */
    public function deletePengajuan(int $id): RedirectResponse
    {
        $pengajuan = PengajuanLayanan::findOrFail($id);
        $kode = $pengajuan->kode_tiket;
        $pengajuan->delete();

        return back()->with('info', "Pengajuan [{$kode}] berhasil dihapus dari database.");
    }

    /**
     * Manajemen Daftar Mahasiswa & Pengguna Terdaftar.
     */
    public function users(Request $request): View
    {
        $query = User::query();

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'mahasiswa' => User::where('role', '!=', 'admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    /**
     * Manajemen Katalog 18 Layanan Mahasiswa UMPKU.
     */
    public function layanan(): View
    {
        $layanans = Layanan::withCount('pengajuan')->orderBy('id')->get();

        return view('admin.layanan', compact('layanans'));
    }

    /**
     * Toggle Status Aktif / Nonaktif Layanan.
     */
    public function toggleLayanan(int $id): RedirectResponse
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->status = ($layanan->status === 'aktif') ? 'nonaktif' : 'aktif';
        $layanan->save();

        return back()->with('success', "Status layanan [{$layanan->nama}] diubah menjadi: " . strtoupper($layanan->status));
    }

    /**
     * CMS Hero Banner & Pengaturan Konten Landing Page.
     */
    public function cms(): View
    {
        $landingContent = LandingContent::all()->keyBy('key');

        return view('admin.cms', compact('landingContent'));
    }

    /**
     * Simpan Perubahan CMS Konten.
     */
    public function updateCms(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:500'],
            'hero_announcement' => ['nullable', 'string', 'max:500'],
            'hero_banner_image' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($validated as $key => $value) {
            LandingContent::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'type' => 'text']
            );
        }

        return back()->with('success', 'Konfigurasi Banner Hero & Konten Landing Page berhasil diperbarui!');
    }

    /**
     * Dokumentasi & Panduan REST API.
     */
    public function apiDocs(): View
    {
        return view('admin.api');
    }
}
