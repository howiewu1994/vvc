<?php
namespace VVC\Controller;

use VVC\Model\Database\Creator;
use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
use VVC\Model\Database\Updater;

/**
 * Admin controller for managing drugs
 */
class DrugManager extends AdminController
{
    public function showDrugListPage()
    {
        try {
            $dbReader = new Reader();
            $drugs = $dbReader->getAllDrugs();

            $ills = [];
            foreach ($drugs as $drug) {
                $ills[$drug->getId()]
                    = $dbReader->findIllnessesByDrugId($drug->getId());
            }
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to get all drugs', $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showDashboardPage();
        }

        $ymls = Uploader::getFiles(YML_DIRECTORY, ['yml']);
        $this->addTwigVar('files', $ymls);

        $this->setTemplate('admin_drugs.twig');
        $this->addTwigVar('drugs', $drugs);
        $this->addTwigVar('ills', $ills);

        $this->render();
    }

    public function showAddDrugPage()
    {
        $this->setTemplate('add_drug.twig');

        $pics = Uploader::getFiles(DRUG_DIRECTORY, ['png', 'jpg']);
        $this->addTwigVar('pics', $pics);

        $this->render();
    }

    public function addDrug(array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showAddDrugPage();
        }

        $name = $post['name'];
        $text = $post['text'];
        $picture = DRUG_DIRECTORY . $post['picture'];
        $cost = $post['cost'];

        try {
            $dbReader = new Reader();
            $drug = $dbReader->findDrugByName($name);

            if (!empty($drug)) {
                $this->flash('fail', "This drug already exists - $name");
                return $this->showAddDrugPage();
            }

            $dbCreator = new Creator();
            $drug = $dbCreator->createDrug($name, $text, $picture, $cost);

            $this->flash('success', "$name added successfully");
            return Router::redirect('/admin/drugs');

        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to create drug $name (single)", $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showAddDrugPage();
        }
    }

    public function batchAddDrugs(array $drugs)
    {
        try {
            $dbReader = new Reader();
            $dbCreator = new Creator();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to open connection', $e);
            $this->flash('fail', 'Database connection failed');
            return Router::redirect('/admin/drugs');
        }

        $good = [];
        $bad = [];

        foreach ($drugs as $drug) {
            if (empty($drug['name'])
                || empty($drug['text'])
                || empty($drug['picture'])
                || empty($drug['cost'])
            ) {
                $this->flash('fail', 'Some data is wrong or missing');
                return Router::redirect('/admin/drugs');
            }

            if (!$this->isClean($drug)) {
                $bad['data'][] = $drug;
                continue;
            }

            $name = $drug['name'];
            $text = $drug['text'];
            $picture = DRUG_DIRECTORY . $drug['picture'];
            $cost = $drug['cost'];

            try {
                $duplicate = $dbReader->findDrugByName($name);

                if ($duplicate) {
                    $bad['duplicate'][] = $drug;
                    continue;
                }

                $newDrug = $dbCreator->createDrug(
                    $name,
                    $text,
                    $picture,
                    $cost
                );

                $good[] = $newDrug;

            } catch (\Exception $e) {
                Logger::log(
                    'db', 'error',
                    "Failed to create drug $name (batch)",
                    $e
                );
                $bad['db'][] = $drug;
                continue;
            }
        }

        $this->prepareGoodBatchResults($good, $drugs, ['id', 'name']);
        $this->prepareBadBatchResults($bad, $drugs, ['name']);

        return Router::redirect('/admin/drugs');
    }

    public function showChangeDrugPage(string $drugId)
    {
        try {
            $dbReader = new Reader();
            $drug = $dbReader->findDrugById($drugId);
        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to find drug by id $drugId", $e);
            $this->flash('fail', 'Database operation failed');
            Router::redirect('/admin/drugs');
        }

        if (empty($drug)) {
            $this->flash('fail', "Drug $drugId not found");
            Router::redirect('/admin/drugs');
        }

        $pics = Uploader::getFiles(DRUG_DIRECTORY, ['png', 'jpg']);
        $this->addTwigVar('pics', $pics);

        $this->setTemplate('change_drug.twig');
        $this->addTwigVar('drug', $drug);
        $this->render();
    }

    public function changeDrug(string $drugId, array $post)
    {
        if (!$this->isClean($post)) {
            $this->flash('fail', 'Input contains invalid characters');
            return $this->showChangeDrugPage($drugId);
        }

        $name = $post['name'];
        $text = $post['text'];
        $picture = DRUG_DIRECTORY . $post['picture'];
        $cost = $post['cost'];

        try {
            $dbReader = new Reader();

            $old = $dbReader->findDrugById($drugId);
            if (empty($old)) {
                $this->flash('fail', 'Some problem occurred, please try again');
                return $this->showChangeDrugPage($drugId);
            }

            if ($old->getName() != $name) {
                // check for duplicate name
                $duplicate = $dbReader->findDrugByName($name);
                if (!empty($duplicate)) {
                    $this->flash('fail', 'This drug name already exists');
                    return $this->showChangeDrugPage($drugId);
                }
            }

            $dbUpdater = new Updater();
            $dbUpdater->updateDrug($drugId, $name, $text, $picture, $cost);

            $this->flash('success', "Drug $name updated");
            return Router::redirect('/admin/drugs');

        } catch (\Exception $e) {
            Logger::log('db', 'error', "Failed to change drug $drugId", $e);
            $this->flash('fail', 'Database operation failed');
            return $this->showChangeDrugPage($drugId);
        }
    }

    public function deleteDrug(string $drugId)
    {
        try {
            $dbDeleter = new Deleter();
            $deleted = $dbDeleter->deleteDrug($drugId);
            if (!$deleted) {
                $this->flash('fail', "Could not delete drug $drugId, try again");
                return Router::redirect('/admin/drugs');
            }

            $this->flash('success', "Drug $drugId deleted");
            return Router::redirect('/admin/drugs');

        } catch (\Exception $e) {
            Logger::log('db', 'error',
                "Failed to delete drug $drugId (single)", $e
            );
            $this->flash('fail', 'Database operation failed');
            return Router::redirect('/admin/drugs');
        }
    }

    public function deleteDrugs(array $drugs)
    {
        $good = [];
        $bad = [];

        foreach ($drugs as $drugId) {
            try {
                $dbDeleter = new Deleter();
                $deleted = $dbDeleter->deleteDrug($drugId);

                if (!$deleted) {
                    $bad['db'][] = $drugId;
                } else {
                    $good[] = $deleted;
                }
            } catch (\Exception $e) {
                Logger::log('db', 'error',
                    "Failed to delete drug $drugId (batch)", $e
                );
                $bad['db'][] = $drugId;
            }
        }

        $this->prepareGoodBatchResults($good, $drugs, ['id', 'name']);
        $this->prepareBadBatchResults($bad, $drugs, ['id']);

        return Router::redirect('/admin/drugs');
    }
}
