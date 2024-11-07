<?php

namespace App\Core;

use Exception;

class ValidationException extends Exception
{
    protected $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation errors', 422);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
