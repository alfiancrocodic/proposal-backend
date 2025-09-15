<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel main_modules
     */
    public function up(): void
    {
        Schema::create('main_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('Nama main module');
            $table->text('description')->nullable()->comment('Deskripsi main module');
            $table->boolean('is_active')->default(true)->comment('Status aktif main module');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan main module');
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi query
            $table->index(['is_active', 'sort_order']);
            $table->index('name');
        });
    }

    /**
     * Rollback migration untuk menghapus tabel main_modules
     */
    public function down(): void
    {
        Schema::dropIfExists('main_modules');
    }
};