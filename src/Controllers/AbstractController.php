<?php

namespace Application\Controllers;

use Application\Lib\DatabaseConnection;
use Application\Lib\Request;
use PDO;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    public function render(string $string, array $array = [])
    {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader, [
            'debug' => true
        ]);

        $twig->addExtension(new DebugExtension());

        return $twig->render($string, $array);
    }
}
