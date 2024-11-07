<?php

namespace App\Http\Requests;

class TokenUserRequest extends FormRequest
{
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
            //'algorithm' => 'required' 
        ];

        return $rules;
    }

    protected function messages(): array
    {
        return [
            '//algorithm.required' => 'El campo algorithm es obligatorio.', 
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
