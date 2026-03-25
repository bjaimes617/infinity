<?php

namespace App\Exceptions;

use Exception;

class ValidateAditionals extends Exception
{
    protected $errors;
     
    public function __construct(array $errors)
    {
        parent::__construct("Errores de validación, Valores recibidos no son Array.");
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
