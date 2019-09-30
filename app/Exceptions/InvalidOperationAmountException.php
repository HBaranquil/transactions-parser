<?php

namespace App\Exceptions;

use Exception;

class InvalidOperationAmountException extends Exception
{
    protected $message = "Invalid operation amount.";
}
