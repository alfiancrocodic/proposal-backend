<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProposalContent;
use Illuminate\Http\Request;

class ProposalContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', ProposalContent::class);
        return ProposalContent::with('proposal')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ProposalContent::class);
        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'data' => 'required|array',
        ]);

        $proposalContent = ProposalContent::create($validated);

        return $proposalContent;
    }

    /**
     * Display the specified resource.
     */
    public function show(ProposalContent $proposalContent)
    {
        $this->authorize('view', $proposalContent);
        return $proposalContent->load('proposal');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProposalContent $proposalContent)
    {
        $this->authorize('update', $proposalContent);
        $validated = $request->validate([
            'proposal_id' => 'sometimes|required|exists:proposals,id',
            'data' => 'sometimes|required|array',
        ]);

        $proposalContent->update($validated);

        return $proposalContent;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProposalContent $proposalContent)
    {
        $this->authorize('delete', $proposalContent);
        $proposalContent->delete();

        return response()->noContent();
    }
}
