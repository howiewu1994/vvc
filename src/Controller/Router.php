<?php
namespace VVC\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Processes htpp request and sends http response
 */
class Router
{
    /**
     * Finds appropriate controller based on request uri
     * and decides what method to call based on get&post data
     */
    public static function run()
    {
        global $req;
        global $session;

        $get = $req->query->all();
        $post = $req->request->all();
        $uri = $req->getPathInfo();

        switch ($uri) {
            case '/' :
                $controller = new BaseController('home.twig');
                $controller->showHomepage();
                break;
            case '/login' :
                $controller = new LoginController('login.twig');

                if (empty($post)) {
                    $controller->showLoginPage();
                } else {
                    $controller->login($post);
                }
                break;
            case '/registration' :
                $controller = new RegistrationController('registration.twig');

                if (empty($post)) {
                    $controller->showRegistrationPage();
                } else {
                    $controller->register($post);
                }
                break;
            case '/3d' :
                $controller = new NavigationController('navigation.twig');
                $controller->showSelectRolePage();
                break;
            case '/catalog' :
                $controller = new CatalogController('catalog.twig');
                $controller->showCatalogPage();
                break;
            default:
                // 404 NOT_FOUND
        }
    }

    /**
     * Prepares and sends http response
     * @param  [type] $html     - rendered twig output
     * @param  int $httpCode    - HTTP_FOUND, HTTP_NOT_FOUND, etc
     * @param  array $headers   - optional http headers
     * @param  array $authToken - optional authentication token
     */
    public static function sendResponse(
        $html,
        $httpCode = Response::HTTP_FOUND,
        array $headers = [],
        $authToken = null)
    {
        $response = new Response($html, $httpCode, $headers);

        if ($authToken) {
            $response->headers->setCookie($authToken);
        }

        $response->send();
    }

    /**
     * Internal redirect to process request at another Location
     * @param  string $uri
     * @param  Cookie Object $authToken
     */
    public static function redirect(string $uri, $authToken)
    {
        self::sendResponse(
            null,
            Response::HTTP_FOUND,
            ['location' => $uri],
            $authToken
        );
    }
}
