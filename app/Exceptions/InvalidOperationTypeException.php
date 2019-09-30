<?php

namespace App\Exceptions;

use Exception;

class InvalidOperationTypeException extends Exception
{
    protected $message = "Invalid operation type.";
}
