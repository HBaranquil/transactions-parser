<?php

namespace App\Exceptions;

use Exception;

class OperationCurrencyNotFoundException extends Exception
{
    protected $message = 'Operation currency not found.';
}
