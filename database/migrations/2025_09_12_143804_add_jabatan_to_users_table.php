<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom jabatan ke tabel users
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hanya menambahkan kolom jabatan, kolom name sudah ada
            $table->string('jabatan')->default('')->after('email');
        });
    }

    /**
     * Menghapus kolom jabatan dari tabel users
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });
    }
};
