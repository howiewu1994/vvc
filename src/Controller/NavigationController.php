<?php
namespace VVC\Controller;

use VVC\Controller\Router;
use VVC\Controller\BaseController;

class NavigationController extends BaseController
{
    public function __construct(Router $router, $template)
    {
        parent::__construct($router, $template);
    }

    public function showSelectRolePage()
    {
        $html = $this->page->render($this->getVars());

        $this->router->sendResponse($html);
    }

}
