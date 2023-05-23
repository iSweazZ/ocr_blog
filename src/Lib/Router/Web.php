<?php

namespace Application\Lib\Router;

use Application\Controllers\AuthentificationController;
use Application\Controllers\CommentsController;
use Application\Controllers\Contact;
use Application\Controllers\HomeController;
use Application\Controllers\PostsController;

class Web
{
    /**
     * @return Route[]
     */
    public static function getRoutes(): array
    {
        return [
            new Route("/", "GET", [], 'index', HomeController::class),
            new Route("/articles", "GET", [], 'showArchived', PostsController::class),
            new Route("/ajout-article", "GET", [], 'addPost', PostsController::class),
            new Route("/ajout-article", "POST", [], 'addPost', PostsController::class),
            new Route("/contact", "GET", [], 'execute', Contact::class),
            new Route("/contact", "POST", [], 'execute', Contact::class),
            new Route("/inscription", "GET", [], 'register', AuthentificationController::class),
            new Route("/inscription", "POST", [], 'register', AuthentificationController::class),
            new Route("/connexion", "GET", [], 'login', AuthentificationController::class),
            new Route("/connexion", "POST", [], 'login', AuthentificationController::class), ///////////
            new Route("/deconnexion", "GET", [], 'logout', AuthentificationController::class),
            new Route("/gestion-commentaires", "GET", [], 'ManageComments', CommentsController::class),
            new Route("/article/:identifiant", "GET", ["id"], 'showPost', PostsController::class),
            new Route("/article/modification/:identifiant", "GET", [], 'updatePost', PostsController::class),
            new Route("/article/modification/:identifiant", "POST", [], 'updatePost', PostsController::class), /////////
            new Route("/article/suppression/:identifiant", "GET", [], 'deletePost', PostsController::class),
            new Route("/article/suppression/:identifiant", "POST", [], 'deletePost', PostsController::class), //////////
            new Route("/article/ajout-commentaire/:idComment", "GET", [], 'addComment', CommentsController::class),
            new Route("/article/ajout-commentaire/:idComment", "POST", [], 'addComment', CommentsController::class), //////////
            new Route("/commentaire/suppression/:identifiant", "GET", [], 'deleteComment', CommentsController::class),
            new Route("/commentaire/suppression/:identifiant", "POST", [], 'deleteComment', CommentsController::class), ///////////
            new Route("/commentaire/modification/:identifiant", "GET", [], 'updateComment', CommentsController::class),
            new Route("/commentaire/modification/:identifiant", "POST", [], 'updateComment', CommentsController::class), //////////
            new Route("/commentaire/validation/:identifiant", "GET", [], 'showComment', CommentsController::class),
            new Route("/commentaire/annulation/:identifiant", "GET", [], 'hideComment', CommentsController::class),
        ];
    }
}
