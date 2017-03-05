<?php
namespace VVC\Controller;

use VVC\Controller\Router;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    protected $router;
    protected $twig;
    protected $page;

    protected $template;
    protected $httpCode = Response::HTTP_FOUND;
    protected $flashes;
    protected $vars = [];

    public function __construct(Router $router, $template = 'home.twig')
    {
        $this->router = $router;
        $this->flashes = $router->getSession()->getFlashBag();

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
            'authenticated', array($this, 'isAuthenticated')
        ));
        $this->twig->addFunction(new \Twig_Function(
            'admin', array($this, 'isAdmin')
        ));

        $this->page = $this->twig->load($this->getTemplate());
    }

    public function render()
    {
        $this->addFlashMessages();
        $html = $this->page->render($this->getVars());
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

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getVars() : array
    {
        return $this->vars;
    }

    public function addVar(string $varName, $varValue)
    {
        $this->vars[$varName] = $varValue;
    }

    public function isAuthenticated()
    {
        return false;
    }

    public function isAdmin()
    {
        if (!isAuthenticated()) {
            return false;
        }

        return true;
    }

    function addFlashMessages()
    {
        $session = $this->router->getSession();;

        $messages = [];
        foreach ($session->getFlashBag()->all() as $msgType => $msg) {
            $messages[$msgType] = $msg;
        }
        //  print_r($messages);
        //  exit;

        $this->addVar('messages', $messages);
    }

    public function isClean(array $vars) : bool
    {
        $beforeCleanup = $vars;
        $this->cleanupVars($vars);

        return $beforeCleanup == $vars;
    }

    public function cleanupVars(&$vars)
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
