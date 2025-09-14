<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalSectionRow extends Model
{
    use HasFactory;

    protected $fillable = ['section_id','values','sort_order','is_active'];

    protected $casts = [
        'values' => 'array',
        'is_active' => 'boolean',
    ];
}

