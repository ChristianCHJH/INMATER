<?php

namespace App\Http\Requests;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'details' => 'required'
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'details.required' => 'The details field is required.'
        ];
    }
}
