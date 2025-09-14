<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalSimpleOption extends Model
{
    use HasFactory;

    protected $fillable = ['section_key','label','sort_order','is_active','is_other'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_other' => 'boolean',
    ];
}

