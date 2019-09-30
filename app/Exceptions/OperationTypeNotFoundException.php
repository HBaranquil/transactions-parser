<?php

namespace App\Exceptions;

use Exception;

class OperationTypeNotFoundException extends Exception
{
    protected $message = "Operation type not found.";
}
