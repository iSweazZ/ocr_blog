<?php

namespace Application\Model;

class User
{
    public int $identifier;
    public string $username;
    public string $email;
    public string $role;
    public string $password;
    public ?string $token;
    public string $lastAuthentication;
    public int $lastAction;
}
