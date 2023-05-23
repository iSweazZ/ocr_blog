<?php

namespace Application\Controllers;

use Application\Exception\AuthentificationException;
use Application\Exception\BadRequestException;
use Application\Lib\CheckUserRole;
use Application\Lib\ManageSession;
use Application\model\Repository\UserRepository;
use DateTimeImmutable;

class AuthentificationController extends AbstractController
{
    public function login()
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $userRepository = new UserRepository();
        $userRole = new CheckUserRole();

        // Redirect user to homepage if already authenticated
        if ($userRole->isAuthenticated($_SESSION['token'] ?? '')) {
            header(sprintf('Location: /'));
        }

        // Secure datas before sending them to database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (is_string($_POST['email'])) {
                // Check if valid email address
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $email = strip_tags($_POST['email']);
                }
            }

            if (is_string($_POST['password'])) {
                $password = strip_tags($_POST['password']);
            }

            $success = $userRepository->login($email, $password);

            if ($success === null) {
                throw new AuthentificationException('Authentification impossible !');
            }
            $user = $userRepository->getUserFromEmail($email);
            $_SESSION['is_authenticated'] = true;
            $_SESSION['id'] = $user->identifier;
            $_SESSION['username'] = $user->username;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = $user->role;
            $_SESSION['time'] = time();
            $now = new DateTimeImmutable();
            $_SESSION['last_authentication'] = $now->format('d/m/Y à H:i:s');
            $_SESSION['token'] = bin2hex(random_bytes(16));
            $userRepository->setToken($_SESSION['token'], $_SESSION['last_authentication'], $user->identifier);

            header('Location: /');
        }


        echo $this->render('login.twig');
    }


    public function logout()
    {
        session_start();
        $userRepository = new UserRepository();

        // Empty token and last action to null in database
        $userRepository->setToken(null, $_SESSION['last_authentication'], $_SESSION['id']);
        $userRepository->setLastAction(null, $_SESSION['id']);

        // Disconnect user, remove session
        session_unset();
        session_destroy();

        header('Location: /');
    }


    public function register()
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $userRepository = new UserRepository();
        $userRole = new CheckUserRole();

        // Redirect user to homepage if already authenticated
        if ($userRole->isAuthenticated($_SESSION['token'] ?? '')) {
            header(sprintf('Location: /'));
        }

        // Secure datas before sending them to database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (is_string($_POST['username'])) {
                $username = strip_tags($_POST['username']);
            }

            if (is_string($_POST['email'])) {
                // Check if valid email address
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $email = strip_tags($_POST['email']);
                }
            }

            if (is_string($_POST['password'])) {
                $password = strip_tags($_POST['password']);
            }

            $role = 'User';

            $success = $userRepository->createUser($username, $email, $role, $password);

            if (!$success) {
                throw new BadRequestException('Impossible d\'ajouter l\'utilisateur !');
            } else {
                header('Location: /');
            }
        }


        echo $this->render('register.twig', ['users' => $userRepository->getUsers()]);
    }
}