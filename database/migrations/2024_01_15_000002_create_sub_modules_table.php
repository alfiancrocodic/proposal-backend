<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel sub_modules
     */
    public function up(): void
    {
        Schema::create('sub_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_module_id')
                  ->constrained('main_modules')
                  ->onDelete('cascade')
                  ->comment('Foreign key ke tabel main_modules');
            $table->string('name', 255)->comment('Nama sub module');
            $table->text('description')->nullable()->comment('Deskripsi sub module');
            $table->boolean('is_active')->default(true)->comment('Status aktif sub module');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan sub module');
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi query
            $table->index(['main_module_id', 'is_active', 'sort_order']);
            $table->index('name');
        });
    }

    /**
     * Rollback migration untuk menghapus tabel sub_modules
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_modules');
    }
};