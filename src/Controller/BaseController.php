<?php
namespace VVC\Controller;

use Symfony\Component\HttpFoundation\Response;

const TEMPLATES_DIR = __DIR__ . '/../View/templates';

/**
 * Basic template for all page controllers
 * Loads twig, wraps all needed variables via render()
 * and transfers response via Router
 *
 * Interfaces: replacing template, replacing http response code,
 * adding variables to twig, adding flash messages to twig,
 * cleaning up input variables
 */
class BaseController extends Auth
{
    protected $twig;
    protected $template;
    protected $httpCode = Response::HTTP_FOUND;
    protected $flashBag;
    protected $vars = [];

    public function __construct(string $template)
    {
        global $session;

        $this->flashBag = $session->getFlashBag();
        $this->template = $template;

        $this->loadTwig();
    }

    public function showHomepage()
    {
        $this->render();
    }

    /**
     * Prepares twig environment and loads default functions to twig,
     * e.g. auth functions
     */
    public function loadTwig()
    {
        $loader = new \Twig_Loader_Filesystem(TEMPLATES_DIR);
        $this->twig = new \Twig_Environment($loader);

        $this->twig->addFunction(new \Twig_Function(
            'authenticated', array('VVC\Controller\Auth', 'isAuthenticated')
        ));
        $this->twig->addFunction(new \Twig_Function(
            'admin', array('VVC\Controller\Auth', 'isAdmin')
        ));
    }

    /**
     * Renders current template, wraps variables for twig and sends response
     */
    public function render()
    {
        $page = $this->twig->load($this->template);
        $this->prepareFlashMessages();
        $html = $page->render($this->vars);
        Router::sendResponse($html);
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    public function setHttpCode(int $httpCode)
    {
        $this->httpCode = $httpCode;
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
    function prepareFlashMessages()
    {
        $messages = [];
        foreach ($this->flashBag->all() as $msgType => $msg) {
            $messages[$msgType] = $msg;
        }
        //  print_r($messages);
        //  exit;

        $this->addVar('messages', $messages);
    }

    /**
     * Cleans up input data and returns true if it has not changed
     * @param  array $vars - by reference -> possibly changed after cleanup
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
