<?php

namespace App\Http\Requests;

use Exception; 
use Illuminate\Validation\Rule;

class MediaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajustar según los permisos requeridos
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|string|max:255',
            'created_by' => 'required|integer',
            'updated_by' => 'nullable|integer',
        ];
    }
}
