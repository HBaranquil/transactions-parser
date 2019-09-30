<?php

namespace App\Exceptions;

use Exception;

class InvalidUserIdentifierException extends Exception
{
    protected $message = 'Invalid user identifier.';
}
