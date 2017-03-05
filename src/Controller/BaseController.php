<?php
namespace VVC\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Basic template for all page controllers
 * Loads twig, wraps all needed variables via render()
 * and transfers response to router
 *
 * Interfaces: replacing template, replacing http response code,
 * adding variables to twig, adding flash messages to twig,
 * cleaning up input variables
 */
class BaseController extends Auth
{
    protected $router;
    protected $auth;
    protected $twig;

    protected $template;
    protected $httpCode = Response::HTTP_FOUND;
    protected $flashes;
    protected $vars = [];

    public function __construct(Router $router, $template = 'home.twig')
    {
        $this->router = $router;
        $this->flashes = $router->getSession()->getFlashBag();

        $this->auth = new Auth($router);

        $this->template = $template;
        $this->loadTwig();
    }

    public function showHomepage()
    {
        $this->render();
    }

    public function loadTwig()
    {
        $loader = new \Twig_Loader_Filesystem(TEMPLATES_DIR);
        $this->twig = new \Twig_Environment($loader);

        $this->twig->addFunction(new \Twig_Function(
            'authenticated', array($this->auth, 'isAuthenticated')
        ));
        $this->twig->addFunction(new \Twig_Function(
            'admin', array($this->auth, 'isAdmin')
        ));
    }

    /**
     * Renders current $template, wraps variables for twig and sends response
     */
    public function render()
    {
        $page = $this->twig->load($this->getTemplate());
        $this->addFlashMessages();
        $html = $page->render($this->getVars());
        $this->router->sendResponse($html);
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function getHttpCode() : int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $httpCode)
    {
        $this->httpCode = $httpCode;
    }

    public function getVars() : array
    {
        return $this->vars;
    }

    /**
     * Adds a key-value pair for future use in a twig template
     * @param string $varName
     * @param any $varValue
     */
    public function addVar(string $varName, $varValue)
    {
        $this->vars[$varName] = $varValue;
    }

    /**
     * Extracts and prepares flash messages for future use in a twig template
     */
    function addFlashMessages()
    {
        $session = $this->router->getSession();

        $messages = [];
        // TODO change flashBag to $this->flashes
        foreach ($session->getFlashBag()->all() as $msgType => $msg) {
            $messages[$msgType] = $msg;
        }
        //  print_r($messages);
        //  exit;

        $this->addVar('messages', $messages);
    }

    /**
     * Cleans up input data and returns true if it has not changed
     * @param  array $vars - by reference
     * @return bool
     */
    public function isClean(array &$vars) : bool
    {
        $beforeCleanup = $vars;
        $this->cleanupVars($vars);

        return $beforeCleanup == $vars;
    }

    public function cleanupVars(array &$vars)
    {
        foreach ($vars as $key => $val) {
            switch ($key) {
                case 'password' :
                    $val = trim(filter_var($val, FILTER_SANITIZE_STRING));
                    $vars[$key] = str_replace(' ', '', $val);
                    break;
                case 'username' :
                    $val = trim(filter_var($val, FILTER_SANITIZE_STRING));
                    $vars[$key] = str_replace(' ', '', $val);
                    break;
                default :
                    $vars[$key] = trim(filter_var($val, FILTER_SANITIZE_STRING));
            }
        }
    }
}
