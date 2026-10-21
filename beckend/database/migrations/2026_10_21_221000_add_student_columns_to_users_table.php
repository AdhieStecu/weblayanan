<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim', 30)->nullable()->unique()->after('email');
            $table->string('program_studi', 100)->nullable()->after('nim');
            $table->unsignedTinyInteger('semester')->nullable()->after('program_studi');
            $table->string('no_hp', 25)->nullable()->after('semester');
            $table->string('avatar_url', 255)->nullable()->after('no_hp');
            $table->string('role', 20)->default('mahasiswa')->after('avatar_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'program_studi', 'semester', 'no_hp', 'avatar_url', 'role']);
        });
    }
};
