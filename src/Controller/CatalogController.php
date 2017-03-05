<?php
namespace VVC\Controller;

use VVC\Controller\Router;
use VVC\Controller\BaseController;

class CatalogController extends BaseController
{
    public function __construct(Router $router, $template)
    {
        parent::__construct($router, $template);
    }

    public function showCatalogPage()
    {
        $html = $this->page->render($this->getVars());

        $this->router->sendResponse($html);
    }

}
