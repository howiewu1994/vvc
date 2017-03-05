<?php
namespace VVC\Controller;

const TEMPLATES_DIR = __DIR__ . '/../View/templates';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Finds appropriate controller based on htpp request uri
 * and sends http response
 */
class Router
{
    private $session;
    private $request;
    private $cookies = [];

    public function __construct($session)
    {
        $this->session = $session;
        $this->request = Request::createFromGlobals();
        $getData = $this->request->query->all();
        $postData = $this->request->request->all();

        $uri = $this->request->getPathInfo();
        switch ($uri) {
            case '/' :
                $controller = new BaseController($this, 'home.twig');
                $controller->showHomepage();
                break;
            case '/login' :
                $controller = new LoginController($this, 'login.twig');

                if (empty($postData)) {
                    $controller->showLoginPage();
                } else {
                    $controller->login($postData);
                }
                break;
            case '/registration' :
                $controller = new RegistrationController($this, 'registration.twig');

                if (empty($postData)) {
                    $controller->showRegistrationPage();
                } else {
                    $controller->register($postData);
                }
                break;
            case '/3d' :
                $controller = new NavigationController($this, 'navigation.twig');
                $controller->showSelectRolePage();
                break;
            case '/catalog' :
                $controller = new CatalogController($this, 'catalog.twig');
                $controller->showCatalogPage();
                break;
            default:
                // 404 NOT_FOUND
        }
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }

    public function getCookies() : array
    {
        return $this->cookies;
    }

    function makeCookies(int $userId, int $userRole)
    {
        $expireTime = time() + 3600;

        $jwt = \Firebase\JWT\JWT::encode([
            'iss'   =>  $this->request->getBaseUrl(),   // issuer (domain, '')
            'uid'   =>  $userId,                        // userId
            'exp'   =>  $expireTime,                    // user id
            'iat'   =>  time(),                         // issued at
            'nbf'   =>  time(),                         // not before (delay)
            'adm'   =>  $userRole == 1                  // is admin or not
        ], getenv('SECRET_KEY'), 'HS256');

        $cookie = new \Symfony\Component\HttpFoundation\Cookie(
            'auth_token',
            $jwt,
            $expireTime,
            '/',
            getenv('COOKIE_DOMAIN')
        );

        $this->cookies[] = $cookie;
    }

    public function sendResponse(
        $html,
        $httpCode = Response::HTTP_FOUND,
        $headers = [])
    {
        $cookies = $this->getCookies();
        $response = new Response($html, $httpCode, $headers);

        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        $response->send();
    }

    /**
     * Internal redirect to process request at another Location
     * @param  string $uri
     */
    public function redirect(string $uri)
    {
        $this->sendResponse(null, Response::HTTP_FOUND, ['location' => $uri]);
    }
}
