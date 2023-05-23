<?php

namespace Application\Controllers;

use Application\Lib\ManageSession;
use Application\Lib\Vue;
use Application\model\Repository\PostRepository;
use DatabaseConnection;

class HomeController extends AbstractController
{
    public function index(ManageSession $manageSession,
                          PostRepository $postRepository)
    {
        $manageSession->execute();
        $posts = array_slice($postRepository->getPosts(), 0, 3);

        foreach ($posts as $post) {
            $post->image === null ? $post->image = 'placeholder-min.jpg' : $post->image;
        }
        echo $this->render('home.twig', ['posts' => $posts, 'session' => $_SESSION]);
    }
}
