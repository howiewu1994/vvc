<?php
namespace VVC\Controller;

use VVC\Model\Database\Creator;
use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;

/**
 * Admin controller to manage illnesses
 */
class IllnessManager extends AdminController
{
    public function showIllnessListPage()
    {
        try {
            $dbReader = new Reader();
            $list = $dbReader->getAllIllnesses();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to get all illnesses', $e);
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
            Logger::log('db', 'error', 'Failed to get full illness by id', $e);
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
