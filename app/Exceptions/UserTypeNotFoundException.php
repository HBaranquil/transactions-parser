<?php

namespace App\Exceptions;

use Exception;

class UserTypeNotFoundException extends Exception
{
    protected $message = "User type not found.";
}
