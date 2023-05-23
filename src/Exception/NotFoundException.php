<?php

namespace Application\Exception;

class NotFoundException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 400);
    }
}
