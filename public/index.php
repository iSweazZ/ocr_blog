<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/..");
require_once '../vendor/autoload.php';

use Application\Lib\Router\Router;
use Dotenv\Dotenv;
$baseDir = substr(__DIR__, 0, strlen(__DIR__) - 7);
$dotenv = Dotenv::createImmutable($baseDir);
$dotenv->load();
$router = new Router();
$router->execute();
