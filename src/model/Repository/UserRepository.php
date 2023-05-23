<?php

namespace Application\model\Repository;

use Application\Lib\DatabaseConnection;
use Application\Model\User;

class UserRepository extends AbstractRepository
{
    private function fetchUser($row): ?User
    {
        $user = new User();
        $user->identifier = $row['id'];
        $user->username = $row['username'];
        $user->email = $row['email'];
        $user->role = $row['role'];

        return $user;
    }

    public function getUserFromId(int $identifier): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT id, username, email, role FROM users WHERE id = ?"
        );

        $statement->execute([$identifier]);
        $row = $statement->fetch();
        //voir pour utiliser fetch object et abstract Class
        if ($row > 0) {
            $result = $this->fetchUser($row);
        } else {
            $result = null;
        }

        return $result;
    }

    public function getUserFromEmail(string $email): User
    {
        $statement = $this->connection->prepare(
            "SELECT id, username, email, role, password FROM users WHERE email = ?"
        );

        $statement->execute([$email]);
        $row = $statement->fetch();

        if ($row > 0) {
            $result = $this->fetchUser($row);
        } else {
            $result = null;
        }

        return $result;
    }

    public function getUsers(): array
    {
        $statement = $this->connection->query(
            "SELECT id, username, email, role, password FROM users"
        );

        $users = [];

        while (($row = $statement->fetch())) {
            $this->fetchUser($row);
            $users[] = $this->fetchUser($row);
        }

        return $users;
    }

    public function createUser(string $username, string $email, string $role, string $password): bool
    {
        $statement = $this->connection->prepare(
            "INSERT INTO users(username, email, role, password) VALUES(?, ?, ?, ?)"
        );

        $affectedLines = $statement->execute([$username, $email, $role, MD5($password)]);

        return ($affectedLines > 0);
    }

    public function login(string $email, string $password): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT id, email, password, username, token FROM users WHERE email = ? AND password = ?"
        );

        $statement->execute([$email, MD5($password)]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        $user = new User();
        $user->email = $row['email'];

        return $user;
    }

    public function setToken(?string $token, string $lastAuthentication, int $identifer): string
    {
        $statement = $this->connection->prepare(
            "UPDATE users set token = ?, last_authentication = ? WHERE id = ?"
        );

        $affectedLines = $statement->execute([$token, $lastAuthentication, $identifer]);

        return ($affectedLines > 0);
    }

    public function checkToken($token): ?User
    {
        $statement = $this->connection->prepare(
            "SELECT id FROM users WHERE token = ?"
        );

        $affectedLines = $statement->execute([$token]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return new User();
    }

    public function setLastAction(?int $datetime, int $identifier): int
    {
        $statement = $this->connection->prepare(
            "UPDATE users set last_action = ? WHERE id = ?"
        );

        $affectedLines = $statement->execute([$datetime, $identifier]);

        return ($affectedLines > 0);
    }

    public function getLastAction(int $identifier): ?int
    {
        $statement = $this->connection->prepare(
            "SELECT last_action FROM users WHERE id = ?"
        );

        $statement->execute([$identifier]);
        $row = $statement->fetch();

        return $row['last_action'];
    }
}
