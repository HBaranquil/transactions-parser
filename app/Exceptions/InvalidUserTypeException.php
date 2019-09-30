<?php

namespace App\Exceptions;

use Exception;

class InvalidUserTypeException extends Exception
{
    protected $message = 'Invalid user type.';
}
