<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket')->unique();
            $table->string('nim', 30);
            $table->string('nama_mahasiswa');
            $table->string('email');
            $table->string('no_hp', 25)->nullable();
            $table->string('fakultas')->nullable();
            $table->string('program_studi');
            $table->integer('semester')->default(1);
            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->nullOnDelete();
            $table->string('jenis_layanan');
            $table->text('keperluan');
            $table->string('dokumen_lampiran')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('catatan_petugas')->nullable();
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_layanan');
    }
};
