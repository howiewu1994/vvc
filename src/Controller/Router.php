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
        $route = self::getRoute();

        switch ($route['base']) {
            case '' :

                $controller = new BaseController();
                $controller->showHomepage();
                break;

            case 'login' :

                $controller = new LoginController();

                if (empty($post)) {
                    $controller->showLoginPage();
                } else {
                    $controller->login($post);
                }
                break;

            case 'registration' :

                $controller = new RegistrationController();

                if (empty($post)) {
                    $controller->showRegistrationPage();
                } else {
                    $controller->register($post);
                }
                break;

            case 'logout' :

                Auth::requireAuth();
                Auth::logout();
                break;

            case 'account' :

                Auth::requireAuth();
                $controller = new AccountController();

                if (empty($post)) {
                    $controller->showChangePasswordPage();
                } else {
                    $controller->changePassword($post);
                }
                break;

            case 'admin' :

                self::routeAdmin($route, $get, $post);
                break;

            case '3d' :

                Auth::requireAuth();
                $controller = new NavigationController();
                $controller->showSelectRolePage();
                break;

            case 'catalog' :

                Auth::requireAuth();
                $controller = new CatalogController();

                if (!empty($route['page']) && is_numeric($route['page'])) {
                    $controller->showIllnessPage($route['page']);
                } else {
                    $controller->showCatalogPage();
                }
                break;

            default:
                // 404 NOT_FOUND
        }
    }

    /**
     *               uri                   action              tepmlate
     * -------------------------------------------------------------------------
     * /                                homepage            homepage.twig
     * /login                           login               login.twig
     * /catalog/1                       show illness        illness.twig
     * /catalog?s=                      search              catalog.twig
     * /admin/accounts                  manage accounts     admin_acc.twig
     * /admin/accounts/1                view account 1      view_acc.twig
     * /admin/accounts/1/change         change account 1    change_acc.twig
     *
     */

    public static function getRoute()
    {
        global $req;

        $route = [];
        $uri = trim($req->getPathInfo(), '/');
        $uri = explode('/', $uri);

        $route['count'] = count($uri);
        $route['base'] = $uri[0];
        $route['section'] = '';
        $route['page'] = '';
        $route['action'] = '';

        if ($route['count'] > 1) {
            $route['section'] = $uri[1];

            if ($route['count'] > 2) {
                $route['page'] = $uri[2];

                if ($route['count'] > 3) {
                    $route['action'] = $uri[3];
                }
            }
        }
        // print_r($uri);
        // print_r($route);
        // exit;

        return $route;
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
        exit;   // close current script execution after redirect
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
    }

    public static function routeAdmin(array $route, array $get, array $post)
    {
        Auth::requireAdmin();
        $controller = new DashboardController();

        switch ($route['section']) {
            case '' :

                $controller->showDashboardPage();
                break;

            case 'accounts' :

                switch ($route['page']) {

                    case '' :
                        $controller->showAccountListPage();
                        break;

                    default :
                        if (!is_numeric($route['page'])) {
                            $controller->showAccountListPage();
                        }
                        switch ($route['action']) {
                            case '' :
                                $controller->showAccountPage($route['page']);
                                break;

                            case 'change' :
                                $controller->showChangeAccountPage(
                                    $route['page'], $post
                                );
                                break;

                            case 'delete' :
                                $controller->deleteAccount($route['page']);
                                break;

                            default :
                                $controller->showAccountPage($route['page']);
                        }
                }

            case 'illnesses' :
                if (!empty($route['page'])
                    && is_numeric($route['page'])) {
                    $controller->showIllnessPage($route['page']);
                } else {
                    $controller->showIllnessListPage();
                }
                break;

            case 'drugs' :

            case 'payments' :

            default :
                $controller->showDashboardPage();
        }
    }
}
