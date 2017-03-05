<?php
namespace VVC\Controller;

use Firebase\JWT\JWT;

/**
 * Handles authentication & authorization
 */
class Auth
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * Makes cookies content available
     * @param  string $field - uid, adm
     * @return whole jwt OR single field value
     */
    function decodeJwt(string $field = null)
    {
        JWT::$leeway = 1;
        $jwt = JWT::decode(
            $this->router->getRequest()->cookies->get('auth_token'),
            getenv('SECRET_KEY'),
            ['HS256']
        );

        if ($field === null) {
            return $jwt;
        }

        return $jwt->{$field};
    }

    public function isAuthenticated() : bool
    {
        // stub for testing
        if (ACCESS_RIGHTS == 0) {
            return false;
        } elseif (ACCESS_RIGHTS > 0) {
            return true;
        }

        if (!$this->router->getRequest()->cookies->has('auth_token')) {
            return false;
        }

        try {
            $this->decodeJwt();
            return true;
        } catch (\Exception $e) {
            // TODO logError($e)
            throw $e;
            return false;
        }
    }

    public function isAdmin() : bool
    {
        // stub for tests
        if (ACCESS_RIGHTS == 1) {
            return true;
        } elseif (ACCESS_RIGHTS == 0 || ACCESS_RIGHTS == 2) {
            return false;
        }

        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $isAdmin = $this->decodeJWT('adm');
            return (boolean)$isAdmin;
        } catch (\Exception $e) {
            // TODO logError($e)
            throw $e;
            return false;
        }
    }

    function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $cookie = new Symfony\Component\HttpFoundation\Cookie(
                'auth_token',
                'Expired',
                time() - 3600,
                '/',
                getenv('COOKIE_DOMAIN')
            );
            $this->router->getSession()->getFlashBag()->add(
                'fail', 'Please sign in first'
            );
            //redirect('login.php', ['cookies' => [$accessToken]]);
        }
        return true;
    }
}
