<?php
namespace VVC\Controller;

class CatalogController extends BaseController
{
    protected $template = 'catalog.twig';

    public function showCatalogPage()
    {
        $this->render();
    }

}
