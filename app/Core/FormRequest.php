<?php

namespace App\Core;

use Exception;
use Rakit\Validation\Validator;

abstract class FormRequest extends Request
{
    abstract public function rules(): array;

    public function validate()
    {
        $validator = new Validator;
        $validation = $validator->make($this->all(), $this->rules());
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            throw new ValidationException($errors->firstOfAll());
        }
    }
}
