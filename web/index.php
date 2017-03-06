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
const ACCESS_RIGHTS = -1;

/**
 * Default user and admin login data
 * Use when ACCESS_RIGHTS = -1
 * and when NO_DATABASE = true;
 */
const ADMIN_NAME     = 'admin';
const ADMIN_PASSWORD = '123';

const USER_NAME      = 'user';
const USER_PASSWORD  = '123';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$session = new Symfony\Component\HttpFoundation\Session\Session();
$session->start();

$req = Symfony\Component\HttpFoundation\Request::createFromGlobals();

VVC\Controller\Router::run();
