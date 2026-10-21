<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanLayanan extends Model
{
    protected $table = 'pengajuan_layanan';

    protected $fillable = [
        'kode_tiket',
        'nim',
        'nama_mahasiswa',
        'email',
        'no_hp',
        'fakultas',
        'program_studi',
        'semester',
        'layanan_id',
        'jenis_layanan',
        'keperluan',
        'dokumen_lampiran',
        'status',
        'catatan_petugas',
        'tanggal_pengajuan',
    ];

    protected $casts = [
        'semester' => 'integer',
        'tanggal_pengajuan' => 'datetime',
    ];

    /**
     * @return BelongsTo<Layanan, $this>
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    /**
     * Generate format kode tiket otomatis: PLM-YYYYMM-XXXX
     */
    public static function generateKodeTiket(): string
    {
        $prefix = 'PLM-' . date('Ym') . '-';
        $latest = static::where('kode_tiket', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d+)$/', (string) $latest->kode_tiket, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
