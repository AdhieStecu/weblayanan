<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLayanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengajuanLayananController extends Controller
{
    /**
     * Menampilkan daftar pengajuan layanan dengan filter & search
     */
    public function index(Request $request): JsonResponse
    {
        $query = PengajuanLayanan::with('layanan');

        // Filter status
        if ($request->filled('status') && $request->query('status') !== 'semua') {
            $query->where('status', strtolower((string) $request->query('status')));
        }

        // Filter jenis layanan
        if ($request->filled('jenis_layanan') && $request->query('jenis_layanan') !== 'semua') {
            $query->where('jenis_layanan', $request->query('jenis_layanan'));
        }

        // Filter riwayat pengajuan milik akun mahasiswa tertentu
        if ($request->filled('email')) {
            $query->where('email', $request->query('email'));
        }
        if ($request->filled('nim')) {
            $query->where('nim', $request->query('nim'));
        }

        // Pencarian berdasarkan NIM, Nama, atau Kode Tiket
        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $pengajuan = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $pengajuan,
            'total' => $pengajuan->count(),
        ]);
    }

    /**
     * Menampilkan statistik ringkasan permohonan layanan
     */
    public function stats(): JsonResponse
    {
        $total = PengajuanLayanan::count();
        $menunggu = PengajuanLayanan::where('status', 'menunggu')->count();
        $diproses = PengajuanLayanan::where('status', 'diproses')->count();
        $selesai = PengajuanLayanan::where('status', 'selesai')->count();
        $ditolak = PengajuanLayanan::where('status', 'ditolak')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $total,
                'menunggu' => $menunggu,
                'diproses' => $diproses,
                'selesai' => $selesai,
                'ditolak' => $ditolak,
            ],
        ]);
    }

    /**
     * Membuat pengajuan layanan baru (CREATE)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nim' => ['required', 'string', 'max:30'],
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'layanan_id' => ['nullable', 'integer', 'exists:layanan,id'],
            'jenis_layanan' => ['required', 'string', 'max:255'],
            'keperluan' => ['required', 'string', 'min:5'],
            'dokumen_lampiran' => ['nullable', 'string', 'max:255'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nama_mahasiswa.required' => 'Nama lengkap mahasiswa wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'semester.required' => 'Semester wajib ditentukan.',
            'jenis_layanan.required' => 'Jenis layanan wajib dipilih.',
            'keperluan.required' => 'Keperluan atau deskripsi pengajuan wajib diisi minimal 5 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        if (empty($validated['layanan_id']) && !empty($validated['jenis_layanan'])) {
            $validated['layanan_id'] = \App\Models\Layanan::where('nama', $validated['jenis_layanan'])->value('id');
        }
        $validated['kode_tiket'] = PengajuanLayanan::generateKodeTiket();
        $validated['status'] = 'menunggu';
        $validated['tanggal_pengajuan'] = now();

        $pengajuan = PengajuanLayanan::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan berhasil dibuat dengan Kode Tiket {$pengajuan->kode_tiket}.",
            'data' => $pengajuan,
        ], 201);
    }

    /**
     * Menampilkan detail satu pengajuan (READ)
     */
    public function show(string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::with('layanan')->find($id);

        if (!$pengajuan) {
            // Coba cari berdasarkan kode_tiket atau NIM
            $pengajuan = PengajuanLayanan::with('layanan')
                ->where('kode_tiket', $id)
                ->orWhere('nim', $id)
                ->first();
        }

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pengajuan,
        ]);
    }

    /**
     * Memperbarui data pengajuan layanan (UPDATE)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nim' => ['sometimes', 'required', 'string', 'max:30'],
            'nama_mahasiswa' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['sometimes', 'required', 'string', 'max:255'],
            'semester' => ['sometimes', 'required', 'integer', 'min:1', 'max:14'],
            'layanan_id' => ['nullable', 'integer', 'exists:layanan,id'],
            'jenis_layanan' => ['sometimes', 'required', 'string', 'max:255'],
            'keperluan' => ['sometimes', 'required', 'string', 'min:5'],
            'dokumen_lampiran' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:menunggu,diproses,selesai,ditolak'],
            'catatan_petugas' => ['nullable', 'string'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nama_mahasiswa.required' => 'Nama mahasiswa wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'status.in' => 'Status hanya boleh: menunggu, diproses, selesai, atau ditolak.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        if (empty($data['layanan_id']) && !empty($data['jenis_layanan'])) {
            $data['layanan_id'] = \App\Models\Layanan::where('nama', $data['jenis_layanan'])->value('id');
        }

        $pengajuan->update($data);

        return response()->json([
            'status' => 'success',
            'message' => "Data pengajuan {$pengajuan->kode_tiket} berhasil diperbarui.",
            'data' => $pengajuan->fresh('layanan'),
        ]);
    }

    /**
     * Menghapus data pengajuan layanan (DELETE)
     */
    public function destroy(string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $kodeTiket = $pengajuan->kode_tiket;
        $pengajuan->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan {$kodeTiket} berhasil dihapus dari sistem.",
        ]);
    }

    /**
     * Memperbarui status pengajuan secara cepat (PATCH)
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:menunggu,diproses,selesai,ditolak'],
            'catatan_petugas' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pengajuan->status = $request->input('status');
        if ($request->has('catatan_petugas')) {
            $pengajuan->catatan_petugas = $request->input('catatan_petugas');
        }
        $pengajuan->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status pengajuan {$pengajuan->kode_tiket} diubah menjadi '{$pengajuan->status}'.",
            'data' => $pengajuan,
        ]);
    }

    /**
     * Setujui pengajuan layanan oleh Administrator (APPROVE)
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $catatan = $request->input(
            'catatan_petugas',
            'Pengajuan telah diverifikasi dan disetujui oleh Administrator. Dokumen resmi telah diterbitkan.'
        );

        $pengajuan->status = 'selesai';
        $pengajuan->catatan_petugas = $catatan;
        $pengajuan->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan {$pengajuan->kode_tiket} berhasil disetujui (Approved)!",
            'data' => $pengajuan->fresh('layanan'),
        ]);
    }

    /**
     * Tolak pengajuan layanan oleh Administrator (REJECT)
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $pengajuan = PengajuanLayanan::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'catatan_petugas' => ['required', 'string', 'min:5'],
        ], [
            'catatan_petugas.required' => 'Alasan penolakan pengajuan wajib diisi.',
            'catatan_petugas.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data penolakan gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pengajuan->status = 'ditolak';
        $pengajuan->catatan_petugas = $request->input('catatan_petugas');
        $pengajuan->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan {$pengajuan->kode_tiket} telah ditolak dengan catatan alasan resmi.",
            'data' => $pengajuan->fresh('layanan'),
        ]);
    }
}
