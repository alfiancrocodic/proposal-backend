<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Proposal::class);
        return Proposal::with('project')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProposalRequest $request)
    {
        $this->authorize('create', Proposal::class);

        $proposal = Proposal::create($request->validated());

        return $proposal;
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        $this->authorize('view', $proposal);
        return $proposal->load('project');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProposalRequest $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $proposal->update($request->validated());

        return $proposal;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        $this->authorize('delete', $proposal);
        $proposal->delete();

        return response()->noContent();
    }
}
