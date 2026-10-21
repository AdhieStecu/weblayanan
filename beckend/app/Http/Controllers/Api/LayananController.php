<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar semua master layanan mahasiswa
     */
    public function index(Request $request): JsonResponse
    {
        $query = Layanan::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->query('kategori'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $layanan = $query->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $layanan,
        ]);
    }

    /**
     * Menampilkan detail satu layanan
     */
    public function show(string $id): JsonResponse
    {
        $layanan = Layanan::find($id);

        if (!$layanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $layanan,
        ]);
    }
}
