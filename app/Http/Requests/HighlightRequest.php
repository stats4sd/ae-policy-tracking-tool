<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HighlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: update
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'policy_document_id' => 'required|integer|exists:policy_documents,id',
            'extract' => 'required|string',
            'start_offset' => 'required|integer',
            'end_offset' => 'required|integer',
            'color' => 'required|string',
        ];
    }
}
