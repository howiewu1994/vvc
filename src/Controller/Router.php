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

                $controller = new BaseController();
                $controller->showHomepage();
                break;

            case '/login' :
                $controller = new LoginController();

                if (empty($post)) {
                    $controller->showLoginPage();
                } else {
                    $controller->login($post);
                }
                break;

            case '/registration' :

                $controller = new RegistrationController();

                if (empty($post)) {
                    $controller->showRegistrationPage();
                } else {
                    $controller->register($post);
                }
                break;

            case '/logout' :

                Auth::requireAuth();
                Auth::logout();
                break;

            case '/account' :

                Auth::requireAuth();
                $controller = new AccountController();

                if (empty($post)) {
                    $controller->showChangePasswordPage();
                } else {
                    $controller->changePassword($post);
                }
                break;

            case '/admin' :

                Auth::requireAdmin();
                $controller = new DashboardController();
                $controller->showDashboardPage();
                break;

            case '/3d' :

                Auth::requireAuth();
                $controller = new NavigationController();
                $controller->showSelectRolePage();
                break;

            case '/catalog' :

                Auth::requireAuth();
                $controller = new CatalogController();
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
    public static function redirect(string $uri, $authToken = null)
    {
        self::sendResponse(
            null,
            Response::HTTP_FOUND,
            ['location' => $uri],
            $authToken
        );
        exit;   // close current script execution after redirect
    }
}
