<?php

namespace App\Exceptions;

use Exception;

class InvalidTransactionDateException extends Exception
{
    protected $message = 'Invalid transaction date.';
}
