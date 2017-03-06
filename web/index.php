<?php
require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("Asia/Shanghai");

// Flags for testing
const NO_DATABASE = true;      // do not use real database connection
/**
 * Access rights (privileges) :
 * -1 - default (need to login)
 *  0 - no privileges on all pages
 *  1 - admin privileges on all pages
 *  2 - signed in user privileges on all pages
 */
const ACCESS_RIGHTS = 0;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$session = new Symfony\Component\HttpFoundation\Session\Session();
$session->start();

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();

$router = new VVC\Controller\Router($session);
