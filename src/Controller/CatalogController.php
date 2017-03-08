<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;

class CatalogController extends BaseController
{
    protected $template = 'catalog.twig';

    public function showCatalogPage()
    {
        try {
            $dbReader = new Reader();
            $catalog = $dbReader->getAllIllnesses();
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            return Router::redirect('/');
        }

        $this->addTwigVar('catalog', $catalog->getRecords());
        $this->render();
    }

    public function showIllnessPage($illnessId)
    {
        if (!is_numeric($illnessId)) {
            return $this->showCatalogPage();
        }

        try {
            $dbReader = new Reader();
            $illness = $dbReader->getFullIllnessById($illnessId);
        } catch (\Exception $e) {
            // TODO logError($e);
            // throw $e;
            $this->flash('fail', 'Database operation failed');
            $this->showCatalogPage();
        }

        if (empty($illness)) {
            $this->flash('fail', 'Could not find illness record');
            $this->showCatalogPage();
        }

        $this->setTemplate('illness.twig');
        $this->addTwigVar('ill', $illness);
        $this->render();
    }
}
