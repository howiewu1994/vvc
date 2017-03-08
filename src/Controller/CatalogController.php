<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;

class CatalogController extends BaseController
{
    protected $template = 'catalog.twig';

    public function showCatalogPage()
    {
        $dbReader = new Reader();
        $catalog = $dbReader->getAllIllnesses();

        $this->addTwigVar('catalog', $catalog->getRecords());
        $this->render();
    }

    public function showIllnessPage(int $illnessId)
    {
        $this->setTemplate('illness.twig');
        
        $dbReader = new Reader();
        $illness = $dbReader->getFullIllnessById($illnessId);

        $this->addTwigVar('ill', $illness);
        $this->render();
    }
}
