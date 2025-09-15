<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainModule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang digunakan oleh model ini
     */
    protected $table = 'main_modules';

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order'
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relasi one-to-many dengan SubModule
     * Satu main module dapat memiliki banyak sub module
     */
    public function subModules()
    {
        return $this->hasMany(SubModule::class, 'main_module_id')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * Relasi many-to-many dengan Feature melalui SubModule
     * Mendapatkan semua features yang terkait dengan main module ini
     */
    public function features()
    {
        return $this->hasManyThrough(
            Feature::class,
            SubModule::class,
            'main_module_id', // Foreign key di sub_modules table
            'sub_module_id',  // Foreign key di features table
            'id',             // Local key di main_modules table
            'id'              // Local key di sub_modules table
        )->where('features.is_active', true)
         ->where('sub_modules.is_active', true);
    }

    /**
     * Scope untuk mendapatkan main module yang aktif
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
     * Accessor untuk mendapatkan jumlah sub modules
     */
    public function getSubModulesCountAttribute()
    {
        return $this->subModules()->count();
    }

    /**
     * Accessor untuk mendapatkan jumlah total features
     */
    public function getTotalFeaturesCountAttribute()
    {
        return $this->features()->count();
    }
}