<?php

namespace App\Exceptions;

use Exception;

class InvalidOperationCurrencyException extends Exception
{
    protected $message = 'Invalid operation currency.';
}
