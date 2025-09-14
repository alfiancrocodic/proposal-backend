<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'sometimes|required|exists:projects,id',
            'nomor_proposal' => 'sometimes|required|string|max:255|unique:proposals,nomor_proposal,' . $this->proposal->id,
            'tanggal_proposal' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|in:draft,sent,accepted,rejected',
        ];
    }
}
