<?php

namespace Application\Exception;

class AuthentificationException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 400);
    }
}
