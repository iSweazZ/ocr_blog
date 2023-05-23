<?php

namespace Application\Exception;

class AccessRightsException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 400);
    }
}
