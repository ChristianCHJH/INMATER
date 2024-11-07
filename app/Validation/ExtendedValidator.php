<?php 
namespace App\Validation;

use Rakit\Validation\Validator as RakitValidator;

class ExtendedValidator extends RakitValidator
{
    public function __construct()
    {
        parent::__construct(); 
        $this->addValidator('string', new StringValidation());  
    }
}
