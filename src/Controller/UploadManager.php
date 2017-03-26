<?php
namespace VVC\Controller;

use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;
use VVC\Model\Database\Creator;
use VVC\Model\Database\Deleter;

class UploadManager extends AdminController
{
    public function showUploadsPage($tab = '', string $picsNum = null)
    {
        if ($picsNum === 'low') {
            $picsPerRow = 4;
            $picsPerPage = $picsPerRow * 3;
        } else {
            $picsNum = 'high';
            $picsPerRow = 6;
            $picsPerPage = $picsPerRow * 4;
        }

        $tab = ($tab == '') ? 'pictures' : $tab;

        $ills = Uploader::getFiles(PIC_DIRECTORY, ['png', 'jpg', 'gif']);
        $drugs = Uploader::getFiles(DRUG_DIRECTORY, ['png', 'jpg', 'gif']);

        for ($i = 0; $i < count($ills); $i++) {
            $illPics[($i/$picsPerPage) + 1][] = $ills[$i];
        }

        for ($i = 0; $i < count($drugs); $i++) {
            $drugPics[($i/$picsPerPage) + 1][] = $drugs[$i];
        }

        try {
            $dbReader = new Reader();
            $ills = $dbReader->getAllIllnesses();
            $steps = $dbReader->getAllSteps();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to get all illnesses', $e);
            $this->flash('fail', 'Database operation failed');
            $this->showDashboardPage();
        }

        $this->addTwigVar('illPics', $illPics);
        $this->addTwigVar('drugPics', $drugPics);
        $this->addTwigVar('ills', $ills->getJustIllnesses());
        $this->addTwigVar('steps', $steps);

        $this->addTwigVar('picsNum', $picsNum);
        $this->addTwigVar('picsPerRow', $picsPerRow);
        $this->addTwigVar('tab', $tab);

        $this->setTemplate('admin_uploads.twig');
        $this->render();
    }

    public function deletePictures(array $pics)
    {
        $good = [];
        $bad = [];

        foreach ($pics as $key => $pic) {
            if (empty($pic)) {
                unset($pics[$key]);
            }
        }

        foreach ($pics as $pic) {
            try {
                $dbDeleter = new Deleter();
                $deletedFromDb = $dbDeleter->deletePicture(PIC_DIRECTORY . $pic);

                if (!$deletedFromDb) {
                    $bad['db'][] = $pic;
                    continue;
                }

                $deletedFromFs = Uploader::deleteFile(PIC_DIRECTORY . $pic);
                if (!$deletedFromFs) {
                    $bad['fs'][] = $pic;
                    continue;
                }

                $good[] = $pic;

            } catch (\Exception $e) {
                Logger::log('db', 'error',
                    "Failed to delete picture $pic", $e
                );
                $bad['db'][] = $pic;
            }
        }

        $this->prepareGoodBatchResults($good, $pics, ['name']);
        $this->prepareBadBatchResults($bad, $pics, ['name']);

        return Router::redirect('/admin/uploads');
    }

}
