<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProposalSection extends Model
{
    use HasFactory;

    protected $fillable = ['key','title','type','description','sort_order','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(ProposalSectionColumn::class, 'section_id')->orderBy('sort_order');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ProposalSectionRow::class, 'section_id')->orderBy('sort_order');
    }

    public function footnotes(): HasMany
    {
        return $this->hasMany(ProposalSectionFootnote::class, 'section_id')->orderBy('sort_order');
    }
}

