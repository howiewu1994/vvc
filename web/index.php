<?php
require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("Asia/Shanghai");

/**
 * TESTING FLAGS
 *
 * Fake database connection
 * You can login as admin or user using details below
 * This is read-only mode, can't modify data
 */
const NO_DATABASE = false;

/**
 * Access rights (privileges) :
 * -1 - default (need to login)
 *  0 - no privileges on all pages
 *  1 - admin privileges on all pages
 *  2 - signed in user privileges on all pages
 */
const ACCESS_RIGHTS = 1;

/**
 * Default user and admin login details
 * Use when ACCESS_RIGHTS = -1
 * and when NO_DATABASE = true;
 */
const ADMIN_NAME     = 'admin';
const ADMIN_PASSWORD = '123';

const USER_NAME      = 'user';
const USER_PASSWORD  = '123';

// Default password when creating users from a batch file
const BATCH_USER_PASSWORD = '123';


$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$session = new Symfony\Component\HttpFoundation\Session\Session();
$session->start();

$req = Symfony\Component\HttpFoundation\Request::createFromGlobals();

VVC\Controller\Router::run();
