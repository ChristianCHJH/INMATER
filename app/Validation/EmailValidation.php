<?php
// app/Validation/EmailValidation.php

namespace App\Validation;

use Rakit\Validation\Rule;

class EmailValidation extends Rule
{
    protected $message = ':attribute debe ser una dirección de correo electrónico válida.';

    public function fillParameters(array $params): Rule
    { 
        return $this;
    }

    public function check($value): bool
    { 
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
