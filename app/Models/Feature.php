<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model ini
     */
    protected $table = 'features';

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'sub_module_id',
        'name',
        'description',
        'is_active',
        'sort_order',
        'mandays'
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu
     */
    protected $casts = [
        'sub_module_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'mandays' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relasi many-to-one dengan SubModule
     * Satu feature belongs to satu sub module
     */
    public function subModule()
    {
        return $this->belongsTo(SubModule::class, 'sub_module_id');
    }

    /**
     * Relasi one-to-many dengan Condition
     * Satu feature dapat memiliki banyak conditions
     */
    public function conditions()
    {
        return $this->hasMany(Condition::class, 'feature_id')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * Relasi many-to-one dengan MainModule melalui SubModule
     * Mendapatkan main module dari feature ini
     */
    public function mainModule()
    {
        return $this->hasOneThrough(
            MainModule::class,
            SubModule::class,
            'id',             // Foreign key di sub_modules table
            'id',             // Foreign key di main_modules table
            'sub_module_id',  // Local key di features table
            'main_module_id'  // Local key di sub_modules table
        );
    }

    /**
     * Scope untuk mendapatkan feature yang aktif
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
     * Scope untuk filter berdasarkan sub module
     */
    public function scopeBySubModule($query, $subModuleId)
    {
        return $query->where('sub_module_id', $subModuleId);
    }

    /**
     * Scope untuk filter berdasarkan main module
     */
    public function scopeByMainModule($query, $mainModuleId)
    {
        return $query->whereHas('subModule', function ($q) use ($mainModuleId) {
            $q->where('main_module_id', $mainModuleId);
        });
    }

    /**
     * Accessor untuk mendapatkan jumlah conditions
     */
    public function getConditionsCountAttribute()
    {
        return $this->conditions()->count();
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dengan hierarki
     */
    public function getFullNameAttribute()
    {
        $subModule = $this->subModule;
        $mainModule = $subModule ? $subModule->mainModule : null;
        
        if ($mainModule && $subModule) {
            return $mainModule->name . ' > ' . $subModule->name . ' > ' . $this->name;
        }
        
        return $this->name;
    }
}