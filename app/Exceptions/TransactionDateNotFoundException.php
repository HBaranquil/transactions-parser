<?php

namespace App\Exceptions;

use Exception;

class TransactionDateNotFoundException extends Exception
{
    protected $message = 'Transaction date not found.';
}
