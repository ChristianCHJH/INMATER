<?php

namespace App\Http\Requests;

use Rakit\Validation\Validator;
use Exception;

class UsuarioRoleRequest extends FormRequest
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
           'usuario_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }
    public function validate()
    { 
        return $this->validated($this->messages());
    } 

    protected function messages(): array
    {
        return [
            'usuario_id.required' => 'El campo usuario_id es obligatorio.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'role_id.required' => 'El campo role_id es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no existe.',
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
