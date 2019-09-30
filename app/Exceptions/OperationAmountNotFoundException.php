<?php

namespace App\Exceptions;

use Exception;

class OperationAmountNotFoundException extends Exception
{
    protected $message = "Operation amount not found.";
}
