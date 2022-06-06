<?php

namespace Codewiser\Enum\Castable\Exceptions;

use Exception;

class InvalidArgumentException extends Exception
{
    protected $message = 'Invalid argument';
}