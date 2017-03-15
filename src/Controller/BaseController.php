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
class BaseController
{
    protected $twig;
    protected $template = 'home.twig';
    protected $httpCode = Response::HTTP_FOUND;
    protected $vars = [];

    protected $logger;

    public function __construct()
    {
        global $logger;
        $this->logger = $logger;

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

        $this->addTwigFunc('authenticated', 'isAuthenticated', 'VVC\Controller\Auth');
        $this->addTwigFunc('admin', 'isAdmin', 'VVC\Controller\Auth');

        $this->twig->addFilter(new \Twig_Filter(
            'short', [$this, 'short']
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
     * Adds a key-value pair for use in a twig template
     * @param string $varName
     * @param any $varValue
     */
    public function addTwigVar(string $varName, $varValue)
    {
        $this->vars[$varName] = $varValue;
    }

    /**
     * Load function for use in twig template
     * @param string $twigFuncName - how to refer inside twig
     * @param string $funcName     - real function name
     * @param $handle
     *        class name if using static function
     *        object instance if using non static function
     *        null if using standalone function
     */
    public function addTwigFunc(
        string $twigFuncName,
        string $funcName,
        $handle = null)
    {
        if ($handle) {
            $this->twig->addFunction(new \Twig_Function(
                $twigFuncName, [$handle, $funcName]
            ));
        } else {
            $this->twig->addFunction(new \Twig_Function(
                $twigFuncName, $funcName
            ));
        }
    }

    /**
     * Shortcut for adding flash messages to
     * the session's flashBag
     * @param string $msgType   - 'success', 'fail'
     * @param string $msg
     */
    public function flash(string $msgType, string $msg)
    {
        global $session;

        $session->getFlashBag()->add($msgType, $msg);
    }

    /**
     * Extracts and prepares flash messages for future use in a twig template
     */
    public function prepareFlashMessages()
    {
        global $session;

        $messages = [];
        foreach ($session->getFlashBag()->all() as $msgType => $msg) {
            $messages[$msgType] = $msg;
        }
        //print_r($messages);
        //exit;

        $this->addTwigVar('messages', $messages);
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
            if (is_array($val)) {
                // p($val);
                $this->cleanupVars($val);
            } else {
                // p($val); n();
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

    /**
     * Returns first 40 characters of a string
     * @param  string $full
     * @return string
     */
    public function short(string $full) : string
    {
        $limit = 55;

        if (strlen($full) >= $limit) {
            return substr($full, 0, $limit-1) . "...";
        } else {
            return substr($full, 0, $limit);
        }
    }
}
