<?php

namespace Codewiser\Enum\Castable\Exceptions;

use Exception;

class NotEnoughArgumentsException extends Exception
{
    protected $message = 'Not enough arguments';
}