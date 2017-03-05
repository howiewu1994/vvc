<?php
require_once __DIR__ . '/../vendor/autoload.php';

const APP_ROOT = '/vvc/web';

use Symfony\Component\HttpFoundation\Session\Session;
use VVC\Controller\Router;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$session = new Session();
$session->start();

$router = new Router($session);
