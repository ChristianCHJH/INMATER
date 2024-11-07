<?php

namespace App\Rules;

use Rakit\Validation\Rule;

class NameOnlyRule extends Rule
{
    protected $message = 'El valor debe ser solo texto.';

    public function check($value): bool
    {
        // Verifica si el valor es una cadena y no contiene números
        return is_string($value) && !preg_match('/\d/', $value);
    }
}