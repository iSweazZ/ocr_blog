<?php

namespace Application\Controllers;

use Application\Exception\AccessRightsException;
use Application\Exception\BadRequestException;
use Application\Exception\LoginException;
use Application\Exception\NotFoundException;
use Application\Lib\CheckUserRole;
use Application\Lib\ManageSession;
use Application\model\Repository\CommentRepository;
use Application\model\Repository\PostRepository;
use Application\model\Repository\UserRepository;

class CommentsController extends AbstractController
{
    public function addComment(int $idComment)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $commentRepository = new CommentRepository();

        if (!$idComment) {
            throw new BadRequestException('Missing ID.');
        }

        $postId = $idComment;

        if ($postId <= 0) {
            throw new BadRequestException('Bad value given for ID.');
        }

        // Check if a comment have been sent
        if (empty($_POST['comment'])) {
            throw new BadRequestException('Les données du formulaire sont invalides.');
        }

        $userId = $_SESSION['id'];
        $comment = $_POST['comment'];
        $status = 0;

        $success = $commentRepository->createComment($postId, $comment, $userId, $status);

        if (!$success) {
            throw new BadRequestException('Impossible d\'ajouter le commentaire !');
        } else {
            header('Location: /article/' . $postId);
        }
    }


    public function deleteComment(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        $userRepository = new UserRepository();
        $userRole = new CheckUserRole();

        // Check if parameter exist and is bigger than zero
        if ($identifiant > 0) {
            $identifier = $identifiant;
        } else {
            throw new BadRequestException('Aucun identifiant de commentaire envoyé');
        }

        $comment = $commentRepository->getComment($identifier);
        $post = $postRepository->getPost($comment->postId);

        // Check if comment author still exists
        if ($userRepository->getUserFromId($comment->userId) !== null) {
            $comment->username = $userRepository->getUserFromId($comment->userId)->username;
        } else {
            $comment->username = 'Compte supprimé';
        }

        // Check if user is authenticated and is comment author or administator
        if (
            ($userRole->isAuthenticated($_SESSION['token'] ?? ''))
            && ($userRole->isAdmin(empty($_SESSION['role']) ? 'Guest' : $_SESSION['role'])
                || $userRole->isCurrentUser($comment->userId, $_SESSION['id'] ?? 0))
        ) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $success = $commentRepository->deleteComment($identifier);

                header(sprintf('Location: /article/%d', $comment->postId));
                if (!$success) {
                    header(sprintf('Location: /article/%d', $comment->postId));
                }
            }
        } else {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }

        // Render vue with Twig

        echo $this->render('delete_comment.twig', ['comment' => $comment, 'post' => $post, 'session' => $_SESSION]);
    }


    public function hideComment(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $commentRepository = new CommentRepository();
        $postRepository = new PostRepository();

        // Check if parameter exist and is bigger than zero
        if (isset($identifiant) && $identifiant > 0) {
            $identifier = $identifiant;

            //            (new HideComment())->execute($identifier);
        } else {
            throw new BadRequestException('Aucun identifiant de commentaire envoyé');
        }

        $userRole = new CheckUserRole();
        $success = false;

        // Check if user is authenticated and administator
        if ($userRole->isAuthenticated($_SESSION['token'] ?? '')) {
            if ($userRole->isAdmin($_SESSION['role']) ?? 'Guest') {
                $comment = $commentRepository->getComment($identifier);
                $postId = $comment->postId;
                $success = $commentRepository->hideComment($identifier);
            }
        } else {
            throw new AccessRightsException('Vous ne pouvez pas masquer ce commentaire !');
        }

        if (!$success) {
            throw new BadRequestException('Impossible de masquer le commentaire !');
        } else {
            header('Location: /article/' . $postId);
        }
    }


    public function ManageComments()
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        $userRepository = new UserRepository();

        $userRole = new CheckUserRole();

        // Check if user is authenticated and administator
        if (($userRole->isAuthenticated($_SESSION['token'] ?? '')) && ($userRole->isAdmin($_SESSION['role']) ?? 'Guest')) {
            $hiddenComments = $commentRepository->getHiddenComments();
            $posts = [];

            foreach ($hiddenComments as $comment) {
                // Check if comment author still exists
                if ($userRepository->getUserFromId($comment->userId) !== null) {
                    $comment->username = $userRepository->getUserFromId($comment->userId)->username;
                } else {
                    $comment->username = 'Compte supprimé';
                }

                $comment->post = $postRepository->getPost($comment->postId);

                // Store comments with hidden status inside associated post array
                if (!in_array($comment->post, $posts)) {
                    $posts[] = $comment->post;
                }
            }

            rsort($posts);

            foreach ($posts as $post) {
                $post->hiddenComments = $commentRepository->getHiddenCommentsFromId($post->identifier);
                $post->username = $userRepository->getUserFromId($post->userId)->username;

                // Check if comment author still exists
                foreach ($post->hiddenComments as $hiddenComment) {
                    if ($userRepository->getUserFromId($hiddenComment->userId) !== null) {
                        $hiddenComment->username = $userRepository->getUserFromId($hiddenComment->userId)->username;
                    } else {
                        $hiddenComment->username = 'Compte supprimé';
                    }
                }
            }
        } else {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }

        echo $this->render('manage_comments.twig', ['posts' => $posts, 'session' => $_SESSION]);
    }


    public function showComment(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $commentRepository = new CommentRepository();
        $postRepository = new PostRepository();

        // Check if parameter exist and is bigger than zero
        if (isset($identifiant) && $identifiant > 0) {
            $identifier = $identifiant;
        } else {
            throw new BadRequestException('Aucun identifiant de commentaire envoyé');
        }

        $userRole = new CheckUserRole();
        $success = false;

        if ($userRole->isAuthenticated($_SESSION['token'] ?? '')) {
            if ($userRole->isAdmin($_SESSION['role']) ?? 'Guest') {
                $success = $commentRepository->showComment($identifier);
            }
        } else {
            throw new LoginException('Vous ne pouvez pas afficher ce commentaire !');
        }

        if (!$success) {
            throw new BadRequestException('Impossible d\'afficher le commentaire !');
        } else {
            header('Location: /gestion-commentaires');
        }
    }


    public function updateComment(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        $userRepository = new UserRepository();

        // Check if parameter exist and is bigger than zero
        if (isset($identifiant) && $identifiant > 0) {
            $identifier = $identifiant;
            // It sets the input only when the HTTP method is POST (ie. the form is submitted).
            $input = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = $_POST;
            }

            //            (new UpdateComment())->execute($identifier, $input);
        } else {
            throw new BadRequestException('Aucun identifiant de commentaire envoyé');
        }

        $comment = $commentRepository->getComment($identifier);
        $post = $postRepository->getPost($comment->postId);
        $userRole = new CheckUserRole();

        // Check if comment author still exists
        if ($userRepository->getUserFromId($comment->userId) !== null) {
            $comment->username = $userRepository->getUserFromId($comment->userId)->username;
        } else {
            $comment->username = 'Compte supprimé';
        }

        if (
            $userRole->isAuthenticated($_SESSION['token'] ?? '')
            && (($userRole->isAdmin($_SESSION['role'] ?? 'Guest')
                || $userRole->isCurrentUser($comment->userId, $_SESSION['id'] ?? 0)))
        ) {
            if ($input !== null) {
                $comment = null;

                if (!empty($input['comment'])) {
                    $comment = $input['comment'];
                } else {
                    throw new BadRequestException('Les données du formulaire sont invalides.');
                }

                $success = $commentRepository->updateComment($identifier, $comment);
                $comment = $commentRepository->getComment($identifier);

                if (!$success) {
                    throw new BadRequestException('Impossible de modifier le commentaire !');
                }
                if ($comment->postId === null) {
                    throw new BadRequestException('Le commentaire concerné n\'existe pas !');
                }

                header(sprintf('Location: /article/%d', $comment->postId));
            }

            if ($comment === null) {
                throw new NotFoundException("Le commentaire $identifier n'existe pas.");
            }
        } else {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }


        echo $this->render('update_comment.twig', ['comment' => $comment, 'post' => $post, 'session' => $_SESSION]);
    }
}