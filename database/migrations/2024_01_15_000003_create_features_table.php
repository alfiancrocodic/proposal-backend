<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel features
     */
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_module_id')
                  ->constrained('sub_modules')
                  ->onDelete('cascade')
                  ->comment('Foreign key ke tabel sub_modules');
            $table->string('name', 255)->comment('Nama feature');
            $table->text('description')->nullable()->comment('Deskripsi feature');
            $table->decimal('mandays', 8, 2)->default(0)->comment('Estimasi mandays untuk feature ini');
            $table->boolean('is_active')->default(true)->comment('Status aktif feature');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan feature');
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi query
            $table->index(['sub_module_id', 'is_active', 'sort_order']);
            $table->index('name');
            $table->index('mandays');
        });
    }

    /**
     * Rollback migration untuk menghapus tabel features
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};