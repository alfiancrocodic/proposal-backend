<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProposalSection;
use App\Models\ProposalSimpleOption;
use App\Models\ProposalContent;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalTemplateController extends Controller
{
    public function templates()
    {
        // Templates are available to authenticated users
        $sections = ProposalSection::with(['columns','rows','footnotes'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $complex = [];
        foreach ($sections as $sec) {
            $complex[$sec->key] = [
                'title' => $sec->title,
                'columns' => $sec->columns->map(fn($c)=>[
                    'key'=>$c->key,'label'=>$c->label,'input_type'=>$c->input_type,'is_checkable'=>(bool)$c->is_checkable
                ])->values(),
                'rows' => $sec->rows->map(fn($r)=>[
                    'values'=> $r->values ?? new \stdClass(),
                    'sort_order'=> $r->sort_order,
                ])->values(),
                'footnotes' => $sec->footnotes->pluck('text')->values(),
            ];
        }

        $keys = ['frontend_lang','app_info','account_availability','db_availability','db_info'];
        $simple = ProposalSimpleOption::whereIn('section_key',$keys)->where('is_active',true)->orderBy('sort_order')->get()->groupBy('section_key');

        return response()->json([
            'complex'=>$complex,
            'simple'=>[
                'frontend_lang'=> ($simple['frontend_lang'] ?? collect())->map(fn($o)=>['label'=>$o->label,'is_other'=>(bool)$o->is_other])->values(),
                'app_info'=> ($simple['app_info'] ?? collect())->map(fn($o)=>['label'=>$o->label])->values(),
                'account_availability'=> ($simple['account_availability'] ?? collect())->map(fn($o)=>['label'=>$o->label])->values(),
                'db_availability'=> ($simple['db_availability'] ?? collect())->map(fn($o)=>['label'=>$o->label])->values(),
                'db_info'=> ($simple['db_info'] ?? collect())->map(fn($o)=>['label'=>$o->label,'is_other'=>(bool)$o->is_other])->values(),
            ]
        ]);
    }

    public function getContent(Proposal $proposal)
    {
        // Allow authenticated to view proposal content snapshot
        $pc = ProposalContent::where('proposal_id',$proposal->id)->first();
        return response()->json($pc?->data ?? []);
    }

    public function putContent(Request $request, Proposal $proposal)
    {
        // Allow authenticated to update proposal content snapshot
        $data = $request->validate(['data'=>'array']);
        $content = ProposalContent::updateOrCreate(['proposal_id'=>$proposal->id],[ 'data'=> $data['data'] ?? [] ]);
        return response()->json(['ok'=>true]);
    }
}
