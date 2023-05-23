<?php

namespace Application\Controllers;

use Application\Exception\AccessRightsException;
use Application\Exception\BadRequestException;
use Application\Lib\CheckUserRole;
use Application\Lib\ManageSession;
use Application\model\Repository\CommentRepository;
use Application\model\Repository\PostRepository;
use Application\model\Repository\UserRepository;
use League\HTMLToMarkdown\HtmlConverter;
use Michelf\Markdown;

class PostsController extends AbstractController
{
    public function addPost()
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $userRole = new CheckUserRole();

        if ($userRole->isAuthenticated($_SESSION['token'] ?? '') && $userRole->isAdmin($_SESSION['role'] ?? 'Guest')) {
            // Check if datas have been sent with post method
            if (!empty($_POST)) {
                $file = $_FILES['file']['error'] !== 0 ? null : $_FILES['file'];

                // Check if an image have been sent
                if (isset($file)) {
                    $tmpName = $_FILES['file']['tmp_name'];
                    $name = $_FILES['file']['name'];
                    $size = $_FILES['file']['size'];
                    $error = $_FILES['file']['error'];

                    $tabExtension = explode('.', $name);
                    $extension = strtolower(end($tabExtension));
                    $mimeType = mime_content_type($tmpName);
                    $extensions = ['image/jpeg', 'image/png', 'image/gif'];
                    $maxSize = 2000000;
                    if (in_array($mimeType, $extensions) && $size <= $maxSize && $error == 0) {
                        $uniqueName = uniqid('', true);
                        $file = $uniqueName . "." . $extension;

                        move_uploaded_file($tmpName, ROOT . '/public/ressources/images/posts/' . $file);
                    } else {
                        throw new BadRequestException('L\'image sélectionnée n\'est pas conforme.');
                    }
                }

                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    $title = $_POST['title'];
                    $content = Markdown::defaultTransform($_POST['content']);
                    $userId = $_SESSION['id'];
                    $status = true;
                } else {
                    throw new BadRequestException('Les données du formulaire sont invalides.');
                }

                $postRepository = new PostRepository();

                $success = $postRepository->createPost($userId, $title, $content, $status, $file);

                header(sprintf('Location: /articles'));
            }
        } else {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }


        echo $this->render('add_post.twig', ['session' => $_SESSION]);
    }


    public function deletePost(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $userRepository = new UserRepository();
        $userRole = new CheckUserRole();

        // Check if parameter exist and is bigger than zero
        if (isset($identifiant) && $identifiant > 0) {
            $identifier = $identifiant;
        } else {
            throw new BadRequestException('Aucun identifiant d\'article envoyé');
        }

        $post = $postRepository->getPost($identifier);

        $post->image === null ? $post->image = 'placeholder-min.jpg' : $post->image;
        $post->username = $userRepository->getUserFromId($post->userId)->username;

        // Check if user is authenticated and administator
        if ($userRole->isAuthenticated($_SESSION['token'] ?? '') && $userRole->isAdmin($_SESSION['role'] ?? 'Guest')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $success = $postRepository->deletePost($identifier);

                if ($success) {
                    if ($post->image !== 'placeholder-min.jpg') {
                        unlink(ROOT . '/public/ressources/images/posts/' . $post->image);
                    }
                    header(sprintf('Location: /articles'));
                } else {
                    header(sprintf('Location: /article/%d', $identifier));
                }
            }
        } else {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }


        echo $this->render('delete_post.twig', ['post' => $post, 'session' => $_SESSION]);
    }


    public function showPost(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        $userRepository = new UserRepository();

        // Check if parameter exist and is bigger than zero
        if (isset($identifiant) && $identifiant > 0) {
            $identifier = $identifiant;
        } else {
            throw new BadRequestException('Aucun identifiant de billet envoyé');
        }

        $post = $postRepository->getPost($identifier);
        $user = $userRepository->getUserFromId($post->userId);
        $post->username = $user->username;
        $comments = $commentRepository->getComments($identifier);
        $visibleComments = [];
        $post->image === null ? $post->image = 'placeholder-min.jpg' : $post->image;

        // Store comments in an array if their status is set on visible
        foreach ($comments as $comment) {
            if ($comment->status === true) {
                if ($userRepository->getUserFromId($comment->userId) !== null) {
                    $user = $userRepository->getUserFromId($comment->userId);
                    $comment->username = $user->username;
                } else {
                    $comment->username = 'Compte supprimé';
                }
                $visibleComments[] = $comment;
            }
        }


        echo $this->render('post.twig', ['post' => $post, 'comments' => array_reverse($visibleComments), 'session' => $_SESSION]);
    }


    public function updatePost(int $identifiant)
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $userRepository = new UserRepository();

        // Check if parameter exist and is bigger than zero
        $identifier = $identifiant;
        // It sets the input only when the HTTP method is POST (ie. the form is submitted).
        $input = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = $_POST;
        }

        $post = $postRepository->getPost($identifier);
        $user = $userRepository->getUserFromId($postRepository->getPost($identifier)->userId)->username;
        $userRole = new CheckUserRole();
        $post->image === null ? $post->image = 'placeholder-min.jpg' : $post->image;
        $htmlToMarkdownConverter = new HtmlConverter(['strip_tags' => true]);
        $post->content = $htmlToMarkdownConverter->convert($post->content);

        if (
            !$userRole->isAuthenticated($_SESSION['token'] ?? '')
            && !$userRole->isAdmin($_SESSION['role'] ?? 'Guest')
        )
        {
            throw new BadRequestException('Les données du formulaire sont invalides.');
        }

        if ($input === null)
        {
            throw new AccessRightsException('Vous n\'avez pas accès à cette page !');
        }
        $content = null;
        $title = null;
        $image = null;

        if (!empty($input['content']) && !empty($input['title'])) {
            $title = $input['title'];
            $content = Markdown::defaultTransform($input['content']);
            $image = $post->image;
            $file = $_FILES['file']['error'] !== 0 ? $image : $_FILES['file'];

            if ($file !== $image) {
                if ($post->image !== 'placeholder-min.jpg') {
                    unlink(ROOT . '/public/ressources/images/posts/' . $post->image);
                };

                $tmpName = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $error = $_FILES['file']['error'];

                $tabExtension = explode('.', $name);
                $extension = strtolower(end($tabExtension));
                $mimeType = mime_content_type($tmpName);
                $extensions = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 1000000;

                if (in_array($mimeType, $extensions) && $size <= $maxSize && $error == 0) {
                    $uniqueName = uniqid('', true);
                    $file = $uniqueName . "." . $extension;

                    move_uploaded_file($tmpName, ROOT . '/public/ressources/images/posts/' . $file);
                } else {
                    throw new BadRequestException('L\'image sélectionnée n\'est pas conforme.');
                }
            }

        $success = $postRepository->updatePost($identifier, $content, $title, $file);

        if (!$success) {
            throw new BadRequestException('Impossible de modifier l\'article !');
        }

        header(sprintf('Location: /article/%d', $identifier));
    }



    echo $this->render('update_post.twig', ['post' => $post, 'user' => $user, 'session' => $_SESSION]);
    }

    public function showArchived()
    {
        $manageSession = new ManageSession();
        $manageSession->execute();
        $postRepository = new PostRepository();
        $userRepository = new UserRepository();

        $posts = $postRepository->getPosts();

        foreach ($posts as $post) {
            $user = $userRepository->getUserFromId($post->userId);
            $post->username = $user->username;
            $post->image === null ? $post->image = 'placeholder-min.jpg' : $post->image;
        }


        echo $this->render('archive.twig', ['posts' => $posts, 'session' => $_SESSION]);
    }
}