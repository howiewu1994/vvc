<?php
namespace VVC\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Processes http request and sends http response
 */
class Router
{
    public static $cookies = [];

    /**
     * Finds appropriate controller based on request uri
     * and decides what method to call based on get&post data
     * @return void
     */
    public static function run()
    {
        global $req;
        global $session;

        $get = $req->query->all();
        $post = $req->request->all();
        $files = $req->files->all();

        if (!empty($req->cookies->get('file_to_overwrite'))) {
            print_r($req->cookies->get('file_to_overwrite'));
        }

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

                self::routeAdmin($route, $get, $post, $files);
                break;

            case '3d' :

                Auth::requireAuth();
                $controller = new NavigationController();
                $controller->showSelectRolePage();
                break;

            case 'catalog' :

                Auth::requireAuth();
                $controller = new CatalogController();

                if (is_numeric($route['page'])) {
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
     * /admin/illnesses                 manage illnesses    admin_ill.twig
     * /admin/illnesses/1               view illness 1      view_ill.twig
     * /admin/illnesses/change/1        change illness 1    change_ill.twig
     *
     */

    /**
     * Extracts route from the request uri
     * @return array - processed route
     */
    public static function getRoute()
    {
        global $req;

        $route = [];
        $uri = trim($req->getPathInfo(), '/');
        $uri = explode('/', $uri);

        $route['base'] = $uri[0];
        $route['section'] = empty($uri[1]) ? '' : $uri[1];
        $route['action'] = empty($uri[2]) ? '' : $uri[2];
        $route['page'] = $uri[count($uri)-1];

        return $route;
    }

    /**
     * Adds cookie to put in the response
     * @param  string $name
     * @param  any $value
     * @return void
     */
    public static function addCookie(string $name, $value)
    {
        self::$cookies[] = [
            'name'  => $name,
            'value' => $value
        ];
    }

    /**
     * Prepares and sends http response and exits script
     * @param  [type] $html     - rendered twig output
     * @param  int $httpCode    - HTTP_FOUND, HTTP_NOT_FOUND, etc
     * @param  array $headers   - optional http headers
     * @param  array $authToken - optional authentication token
     * @return void
     */
    public static function sendResponse(
        $html,
        $httpCode = Response::HTTP_FOUND,
        array $headers = [],
        $authToken = null
    ) {
        $response = new Response($html, $httpCode, $headers);

        $response->headers->clearCookie('file_to_overwrite');

        if ($authToken) {
            $response->headers->setCookie($authToken);
        }

        foreach (self::$cookies as $cookie) {
            $newCookie = new \Symfony\Component\HttpFoundation\Cookie(
                $cookie['name'], $cookie['value']
            );

            $response->headers->setCookie($newCookie);
        }

        $response->send();
        exit;   // close current script execution after redirect
    }

    /**
     * Internal redirect to process request at another Location
     * @param  string $uri
     * @param  Cookie Object $authToken
     * @return void
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

    /**
     * Handles routing inside Admin Dashboard
     * @param  array  $route
     * @param  array  $get   - GET data
     * @param  array  $post  - POST data
     * @param  array  $files - file uploads
     * @return void
     */
    public static function routeAdmin(
        array $route, array $get, array $post, array $files)
    {
        Auth::requireAdmin();
        $controller = new AdminController();

        switch ($route['section']) {
            case '' :

                $controller->showDashboardPage();
                break;

            case 'accounts' :

                $controller = new AccountManager();

                // Check if user id in uri is valid
                if (in_array($route['action'], ['change'])
                    && !is_numeric($route['page'])) {
                    self::redirect('/admin/accounts');
                }

                switch ($route['action']) {
                    case '' :
                        $controller->showAccountListPage();
                        break;

                    case 'add-single' :
                        if (empty($post)) {
                            $controller->showAddAccountPage();
                        } else {
                            $controller->addAccount($post);
                        }

                    case 'add-many' :
                        $accs = Uploader::readAccountsFromYml($controller, $files);
                        $controller->batchAddAccounts($accs);
                        break;

                    case 'change' :
                        if (empty($post)) {
                            $controller->showChangeAccountPage($route['page']);
                        } else {
                            $controller->changeAccount($route['page'], $post);
                        }
                        break;

                    case 'delete' :
                        // print_r($post);exit;
                        if (empty($post['id'])) {
                            $controller->showAccountListPage();
                        } elseif (count($post['id']) == 1){
                            $controller->deleteAccount($post['id'][0]);
                        } else {
                            $controller->deleteAccounts($post['id']);
                        }
                        break;

                    default :
                        self::redirect('/admin/accounts');
                }
                break;

            case 'illnesses' :

            case 'drugs' :

            case 'hospitalization' :

            case 'payments' :

            case 'uploads' :

                // $controller = new UploadManager();
                // $controller->showUploadsPage();
                // break;

            default :
                self::redirect('/admin');
        }
    }
}
