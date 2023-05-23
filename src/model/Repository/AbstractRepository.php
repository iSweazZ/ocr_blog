<?php

namespace Application\model\Repository;

use Application\Lib\DatabaseConnection;
use PDO;

class AbstractRepository
{
    protected PDO $connection;

    public function __construct()
    {
        $this->connection = DatabaseConnection::getConnection();
    }
}
