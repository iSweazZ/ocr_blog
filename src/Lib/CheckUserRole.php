<?php

namespace Application\Lib;

use Application\model\Repository\UserRepository;

class CheckUserRole
{
    // Return user's administration right
    public function isAdmin($user_role): bool
    {
        if ($user_role === 'Admin') {
            return true;
        } else {
            return false;
        }
    }

    // Check current user informations
    public function isCurrentUser(int $userId, int $current_user_id): bool
    {
        if ($userId === $current_user_id) {
            return true;
        } else {
            return false;
        }
    }

    // Check if user is authenticated with token method
    public function isAuthenticated(string $token): bool
    {
        $userRepository = new UserRepository();

        if ($userRepository->checkToken($token)) {
            return true;
        } else {
            return false;
        }
    }
}
