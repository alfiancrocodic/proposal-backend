<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalSectionColumn extends Model
{
    use HasFactory;

    protected $fillable = ['section_id','key','label','input_type','sort_order','is_checkable'];

    protected $casts = [
        'is_checkable' => 'boolean',
    ];
}

