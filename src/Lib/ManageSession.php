<?php

namespace Application\Lib;

use Application\model\Repository\UserRepository;

class ManageSession
{
    public function execute(): array
    {
        session_start();

        // Set user timing informations in database
        if (!empty($_SESSION)) {
            $userRepository = new UserRepository();
            $lastAction = $userRepository->getLastAction($_SESSION['id']);
            $sessionDuration = 10;
            $timeSpend = time() - $lastAction;

            // Disconnect user if time spent since last action is more than $sessionDuration minutes
            if ($lastAction !== null && $timeSpend < ($sessionDuration * 60)) {
                $userRepository->setLastAction(time(), $_SESSION['id']);
            } elseif ($lastAction === null) {
                $userRepository->setLastAction(time(), $_SESSION['id']);
            } else {
                $userRepository->setLastAction(null, $_SESSION['id']);
                $userRepository->setToken(null, $_SESSION['last_authentication'], $_SESSION['id']);
                session_unset();
                session_destroy();
            }
        }

        return $_SESSION;
    }
}
