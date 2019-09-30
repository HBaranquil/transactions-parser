<?php

namespace App\Exceptions;

use Exception;

class UserIdentifierNotFoundException extends Exception
{
    protected $message = 'User identifier not found.';
}
