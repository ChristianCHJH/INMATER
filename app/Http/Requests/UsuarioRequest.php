<?php

namespace App\Http\Requests;

use Exception;
use App\Validation\ExtendedValidator;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    protected $validator;
    protected $messages = [];
    protected $errors = [];
    protected $isUpdate = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function authorize()
    {
        return true;  
    }

    public function rules(): array
    {
        $rules = [
            'userx' => [
                'required',
                'string',
                'max:255',
                //Rule::unique('users', 'userx')->ignore($this->id),
            ],
            'nom' => 'required|string|max:255|name',
            'mail' => [
                'required',
                'email',
                'max:255',
               // Rule::unique('users', 'email')->ignore($this->id),
            ],
            'pass' => 'required|string|min:8',
            'role' => 'required',
            'cmp' => 'required|numeric',
            //'estado' => 'required|in:0,1',
        ];

        if ($this->isUpdate) {
            // Reglas adicionales o diferentes para actualización
            $rules['pass'] = 'nullable|string|min:8';
        } else {
            // Reglas para registro
            $rules['pass'] = 'required|string|min:8';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'userx.required' => 'El campo username es obligatorio.',
            'userx.string' => 'El campo username debe ser una cadena de texto.',
            'userx.max' => 'El campo username no debe tener más de 255 caracteres.',
            'userx.unique' => 'El username ya está en uso.',
            'nom.required' => 'El campo nombre es obligatorio.',
            'nom.string' => 'El campo nombre debe ser una cadena de texto.',
            'nom.max' => 'El campo nombre no debe tener más de 255 caracteres.',
            'mail.required' => 'El campo email es obligatorio.',
            'mail.string' => 'El campo email debe ser una cadena de texto.',
            'mail.email' => 'El campo email debe ser una dirección de correo electrónico válida.',
            'mail.max' => 'El campo email no debe tener más de 255 caracteres.',
            'mail.unique' => 'El email ya está en uso.',
            'pass.required' => 'El campo contraseña es obligatorio.',
            'pass.string' => 'El campo contraseña debe ser una cadena de texto.',
            'pass.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El campo rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no es válido.',
            'cmp.required' => 'El campo cmp es obligatorio.',
            'cmp.numeric' => 'El campo cmp debe ser un número.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser 0 o 1.',
        ];
    }
    
    public function validate(&$validatemsj)
    { 
        $class_css = ['border-green-500','border-red-500','text-green-500','text-red-500'];
        return $this->validated($this->rules(), $this->messages(), $validatemsj, $class_css);
    } 

    public function setUpdate(bool $isUpdate)
    {
        $this->isUpdate = $isUpdate;
    }
}
