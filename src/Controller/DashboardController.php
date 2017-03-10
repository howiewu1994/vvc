<?php
namespace VVC\Controller;

class DashboardController extends BaseController
{
    protected $template = 'dashboard.twig';

    public function showDashboardPage()
    {
        $this->render();
    }

    public function showIllnessListPage()
    {
        try {
            $dbReader = new Reader();
            $list = $dbReader->getAllIllnesses();
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            $this->showDashboardPage();
        }

        $this->setTemplate('illnesses.twig');
        $this->addTwigVar('list', $list->getRecords());
        $this->render();
    }

    public function showIllnessPage($illnessId)
    {
        try {
            $dbReader = new Reader();
            $illness = $dbReader->getFullIllnessById($illnessId);
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            $this->showIllnessListPage();
        }

        if (empty($illness)) {
            $this->flash('fail', 'Could not find illness record');
            $this->showIllnessListPage();
        }

        $this->setTemplate('dashboard_illness.twig');
        $this->addTwigVar('ill', $illness);
        $this->render();
    }

}
