<?php

namespace Application\Exception;

class ControllerException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 400);
    }
}
