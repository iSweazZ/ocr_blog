<?php

namespace Application\Lib;

class Vue
{
    // Render page with Twig template engine
    public function render(string $string, array $array = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, [
            //            'cache' => 'cache',
            'debug' => true
        ]);

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        return $this->render($string, $array);
    }
}
