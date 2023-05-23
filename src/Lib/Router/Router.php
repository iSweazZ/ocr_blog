<?php

namespace Application\Lib\Router;

use Application\Exception\ControllerException;
use Application\Lib\Exception;
use Application\Lib\Request;
use Application\Lib\Vue;

class Router
{
    // Root incoming request : execute associated action
    public function execute()
    {
        try {
            $this->matchRoute();
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    private function matchRoute()
    {
        $requestMethod = (
            isset($_POST['_method'])
            && ($_method = strtoupper($_POST['_method']))
            && in_array($_method, ['PUT', 'DELETE'], true)
        ) ? $_method : $_SERVER['REQUEST_METHOD'];

        $requestUrl = $_SERVER['REQUEST_URI'];

        // strip GET variables from URL
        if (($pos = strpos($requestUrl, '?')) !== false) {
            $requestUrl = substr($requestUrl, 0, $pos);
        }

        return $this->match($requestUrl, $requestMethod);
    }

    private function match(string $requestUrl, string $requestMethod = 'GET')
    {
        $currentDir = dirname($_SERVER['SCRIPT_NAME']);
        $routes = Web::getRoutes();
        foreach ($routes as $route) {
            if ($requestMethod !== $route->getMethod()) {
                continue;
            }

            if ('/' !== $currentDir) {
                $requestUrl = str_replace($currentDir, "", $requestUrl);
            }

            $currentRoute = rtrim($route->getRegex(), '/');
            $pattern = '@^' . preg_quote("") . $currentRoute . '/?$@i';
            if (!preg_match($pattern, $requestUrl, $matches)) {
                continue;
            }

            $params = [];

            if (preg_match_all('/:([\w]+)/', $route->getUrl(), $argumentKeys)) {
                $argumentKeys = $argumentKeys[1];

                if (count($argumentKeys) !== (count($matches) - 1)) {
                    continue;
                }

                foreach ($argumentKeys as $key => $name) {
                    if (isset($matches[$key + 1])) {
                        $params[$name] = $matches[$key + 1];
                    }
                }
            }

            $route->setParams($params);
            $route->dispatchRoute();
        }
    }
}
