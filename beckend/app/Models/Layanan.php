<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'estimasi',
        'persyaratan',
        'status',
        'icon',
    ];

    protected $casts = [
        'persyaratan' => 'array',
    ];

    /**
     * @return HasMany<PengajuanLayanan, $this>
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(PengajuanLayanan::class, 'layanan_id');
    }
}
