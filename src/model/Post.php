<?php

namespace Application\Model;

class Post
{
    public int $identifier;
    public int $userId;
    public string $title;
    public string $content;
    public string $creationDate;
    public string $updateDate;
    public string $username;
    public ?string $image;
    public ?array $hiddenComments;
}
