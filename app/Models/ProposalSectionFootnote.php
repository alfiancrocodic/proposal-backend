<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalSectionFootnote extends Model
{
    use HasFactory;

    protected $fillable = ['section_id','text','sort_order'];
}

