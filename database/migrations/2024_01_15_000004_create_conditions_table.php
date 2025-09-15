<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel conditions
     */
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')
                  ->constrained('features')
                  ->onDelete('cascade')
                  ->comment('Foreign key ke tabel features');
            $table->string('name', 255)->comment('Nama condition/special case');
            $table->text('description')->nullable()->comment('Deskripsi condition');
            $table->text('condition_text')->nullable()->comment('Detail kondisi/special case yang harus dipenuhi');
            $table->boolean('is_active')->default(true)->comment('Status aktif condition');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan condition');
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi query
            $table->index(['feature_id', 'is_active', 'sort_order']);
            $table->index('name');
        });
    }

    /**
     * Rollback migration untuk menghapus tabel conditions
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};