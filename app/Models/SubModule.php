<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubModule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model ini
     */
    protected $table = 'sub_modules';

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'main_module_id',
        'name',
        'description',
        'is_active',
        'sort_order'
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu
     */
    protected $casts = [
        'main_module_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relasi many-to-one dengan MainModule
     * Satu sub module belongs to satu main module
     */
    public function mainModule()
    {
        return $this->belongsTo(MainModule::class, 'main_module_id');
    }

    /**
     * Relasi one-to-many dengan Feature
     * Satu sub module dapat memiliki banyak features
     */
    public function features()
    {
        return $this->hasMany(Feature::class, 'sub_module_id')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * Scope untuk mendapatkan sub module yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope untuk filter berdasarkan main module
     */
    public function scopeByMainModule($query, $mainModuleId)
    {
        return $query->where('main_module_id', $mainModuleId);
    }

    /**
     * Accessor untuk mendapatkan jumlah features
     */
    public function getFeaturesCountAttribute()
    {
        return $this->features()->count();
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dengan main module
     */
    public function getFullNameAttribute()
    {
        return $this->mainModule->name . ' - ' . $this->name;
    }
}