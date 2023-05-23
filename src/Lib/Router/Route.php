<?php

namespace Application\Lib\Router;

use ReflectionClass;

class Route
{
    private array $filters = [];


    public function __construct(
        private string $url,
        private string $method,
        private array $params,
        private string $action,
        private string $controller
    ) {
    }

    public function getRegex()
    {
        return preg_replace_callback('/(:\w+)/', [&$this, 'substituteFilter'], $this->url);
    }

    private function substituteFilter($matches): string
    {
        if (isset($matches[1], $this->filters[$matches[1]])) {
            return $this->filters[$matches[1]];
        }

        return '([\w]+)';
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function dispatchRoute()
    {
        $controller = $this->controller;
        $controller = new $controller();

        $reflectionMethod = (new ReflectionClass($controller))->getMethod($this->action);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            // ça fait office d'un genre de manuel injection
            $class = $parameter->getType()->getName();
            if (
                $class !== 'string' &&
                $class !== 'int' &&
                $class !== 'float'
            ) {
                $this->params[$parameter->getName()] = new $class();
            }
        }

        return call_user_func_array([$controller, $this->action], $this->params);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }
}
