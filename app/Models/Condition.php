<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model ini
     */
    protected $table = 'conditions';

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'feature_id',
        'name',
        'description',
        'condition_text',
        'is_active',
        'sort_order'
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu
     */
    protected $casts = [
        'feature_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relasi many-to-one dengan Feature
     * Satu condition belongs to satu feature
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    /**
     * Relasi many-to-one dengan SubModule melalui Feature
     * Mendapatkan sub module dari condition ini
     */
    public function subModule()
    {
        return $this->hasOneThrough(
            SubModule::class,
            Feature::class,
            'id',           // Foreign key di features table
            'id',           // Foreign key di sub_modules table
            'feature_id',   // Local key di conditions table
            'sub_module_id' // Local key di features table
        );
    }

    /**
     * Relasi many-to-one dengan MainModule melalui Feature dan SubModule
     * Mendapatkan main module dari condition ini
     */
    public function mainModule()
    {
        return $this->feature ? $this->feature->mainModule() : null;
    }

    /**
     * Scope untuk mendapatkan condition yang aktif
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
     * Scope untuk filter berdasarkan feature
     */
    public function scopeByFeature($query, $featureId)
    {
        return $query->where('feature_id', $featureId);
    }

    /**
     * Scope untuk filter berdasarkan sub module
     */
    public function scopeBySubModule($query, $subModuleId)
    {
        return $query->whereHas('feature', function ($q) use ($subModuleId) {
            $q->where('sub_module_id', $subModuleId);
        });
    }

    /**
     * Scope untuk filter berdasarkan main module
     */
    public function scopeByMainModule($query, $mainModuleId)
    {
        return $query->whereHas('feature.subModule', function ($q) use ($mainModuleId) {
            $q->where('main_module_id', $mainModuleId);
        });
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dengan hierarki
     */
    public function getFullNameAttribute()
    {
        $feature = $this->feature;
        if ($feature) {
            return $feature->full_name . ' > ' . $this->name;
        }
        
        return $this->name;
    }

    /**
     * Accessor untuk mendapatkan condition text yang sudah diformat
     */
    public function getFormattedConditionTextAttribute()
    {
        return $this->condition_text ? nl2br(e($this->condition_text)) : '';
    }
}