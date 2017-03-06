<?php
namespace VVC\Controller;

class DashboardController extends BaseController
{
    protected $template = 'dashboard.twig';

    public function showDashboardPage()
    {
        $this->render();
    }

}
