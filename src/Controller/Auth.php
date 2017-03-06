<?php
namespace VVC\Controller;

use Firebase\JWT\JWT;

/**
 * Handles authentication & authorization
 */
class Auth
{
    /**
     * Wraps authentication token in a jwt
     * @param  int    $userId
     * @param  int    $userRole
     * @return Cookie
     */
    public static function encodeToken(int $userId, int $userRole)
    {
        global $req;

        try {
            $expireTime = time() + 3600;    // cookie lives for one hour

            $jwt = \Firebase\JWT\JWT::encode([
                'iss'   =>  $req->getBaseUrl(),     // issuer (domain)
                'exp'   =>  $expireTime,            // user id
                'iat'   =>  time(),                 // issued at
                'nbf'   =>  time(),                 // not before (delay)
                'uid'   =>  $userId,                // user id
                'adm'   =>  $userRole == 1          // is admin or not
            ], getenv('SECRET_KEY'), 'HS256');

            $token = new \Symfony\Component\HttpFoundation\Cookie(
                'auth_token',
                $jwt,
                $expireTime,
                '/',
                getenv('COOKIE_DOMAIN')
            );

            return $token;

        } catch (\Exception $e) {
            // TODO logError($e)
            // throw $e;
            return null;
        }
    }

    /**
     * Makes token content available
     * @param  string $field - uid, adm
     * @return whole jwt OR single field value
     */
    public static function decodeToken(string $field = null)
    {
        global $req;
        JWT::$leeway = 1;   // for sync issues

        try {
            $jwt = JWT::decode(
            $req->cookies->get('auth_token'),
                getenv('SECRET_KEY'),
                ['HS256']
            );

            if ($field === null) {
                return $jwt;
            }

            return $jwt->{$field};

        } catch (\Exception $e) {
            // TODO logError($e)
            // throw $e;
            return false;
        }
    }

    public static function isAuthenticated() : bool
    {
        // stub for testing
        if (ACCESS_RIGHTS == 0) {
            return false;
        } elseif (ACCESS_RIGHTS > 0) {
            return true;
        }

        global $req;

        if (!$req->cookies->has('auth_token')) {
            return false;
        }

        self::decodeToken();
        return true;
    }

    public static function isAdmin() : bool
    {
        // stub for tests
        if (ACCESS_RIGHTS == 1) {
            return true;
        } elseif (ACCESS_RIGHTS == 0 || ACCESS_RIGHTS == 2) {
            return false;
        }

        if (!self::isAuthenticated()) {
            return false;
        }

        $isAdmin = self::decodeToken('adm');
        return (boolean)$isAdmin;
    }

    /**
     * Redirects user to login page if not signed in
     * @return true - if signed in
     */
    public static function requireAuth()
    {
        global $session;

        if (!self::isAuthenticated()) {
            $token = new \Symfony\Component\HttpFoundation\Cookie(
                'auth_token',
                'Expired',
                time() - 3600,
                '/',
                getenv('COOKIE_DOMAIN')
            );
            $session->getFlashBag()->add('success', 'Please sign in first');
            return Router::redirect('/login', $token);
        }

        return true;
    }

    /**
     * Redirects user to homepage if not admin
     * @return true - if admin
     */
    public static function requireAdmin()
    {
        global $session;
        self::requireAuth();

        if (!self::isAdmin()) {
            $session->getFlashBag()->add(
                'fail', 'Not authorized to view this page contents'
            );
            Router::redirect('/');
        }

        return true;
    }
}
