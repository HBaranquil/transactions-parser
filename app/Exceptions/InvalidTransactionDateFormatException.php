<?php

namespace App\Exceptions;

use Exception;

class InvalidTransactionDateFormatException extends Exception
{
    protected $message = 'Invalid transaction date format.';
}
