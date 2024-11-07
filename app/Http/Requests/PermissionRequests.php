<?php

namespace App\Http\Requests;

use Rakit\Validation\Validator;
use Exception;

class PermissionRequest extends FormRequest
{
    protected $validator; 
    protected $messages = [];
    protected $errors = [];

    public function __construct()
    {
        $this->validator = new Validator();
    }
    public function rules(): array
    {
        return [
            'nombre' => 'required|unique:permissions',
            'descripcion' => 'required',
        ];
    }
    public function validate()
    { 
        return $this->validated($this->messages());
    } 

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.unique' => 'El nombre del permiso ya está en uso.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
