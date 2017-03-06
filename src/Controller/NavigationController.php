<?php
namespace VVC\Controller;

class NavigationController extends BaseController
{
    protected $template = 'navigation.twig';

    public function showSelectRolePage()
    {
        $this->render();
    }

}
