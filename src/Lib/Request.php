<?php

namespace Application\Lib;

class Request
{
    public function __construct(private readonly array $parameters)
    {
    }

    public function existParameter(string $name): bool
    {
        return (isset($this->parameters[$name]) && $this->parameters[$name] !== "");
    }

    public function getParameter(string $name): string | int | float | object
    {
        if ($this->existParameter($name)) {
            return $this->parameters[$name];
        }
        throw new \LogicException("Paramètre '$name' absent de la requête");
    }
}
