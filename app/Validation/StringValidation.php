<?php 
namespace App\Validation;

use Rakit\Validation\Rule;

class StringValidation extends Rule
{
    protected $message = ':attribute debe ser una cadena de texto.';

    public function fillParameters(array $params): Rule
    { 
        return $this;
    }

    public function check($value): bool
    { 
        return is_string($value);
    }
}
