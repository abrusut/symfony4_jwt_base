<?php


namespace App\Exception;


use Throwable;

class CustomValidationException extends \Exception
{
    public function __construct($message = "Error de validacion", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}