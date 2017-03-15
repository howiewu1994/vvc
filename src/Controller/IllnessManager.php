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

        $this->setTemplate('admin_ills.twig');
        $this->addTwigVar('list', $list->getJustIllnesses());
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

        $this->setTemplate('illness.twig');
        $this->addTwigVar('ill', $illness);
        $this->render();
    }

    public function batchAddIllnesses(array $ills)
    {
        try {
            $dbReader = new Reader();
            $dbCreator = new Creator();
        } catch (\Exception $e) {
            Logger::log('db', 'error', 'Failed to open connection', $e);
            $this->flash('fail', 'Database connection failed');
            return Router::redirect('/admin/illnesses');
        }

        $good = [];
        $bad = [];
        foreach ($ills as $ill) {
            if (!$this->isClean($ill)) {
                $bad['data'][] = $ill;
                continue;
            }

            $duplicate = $dbReader->findIllnessByName($ill['name']);

            if ($duplicate) {
                $bad['duplicate'][] = $ill;
                continue;
            }

            $newIll = $dbCreator->createFullIllness(
                $ill['name'],
                $ill['class'],
                $ill['description'],
                $ill['steps'],
                $ill['drugs'],
                //$ill['stay'],
                $ill['payments']
            );

            if (!$newIll) {
                Logger::log(
                    'db', 'error',
                    'Failed to create illness from batch file',
                    $e
                );
                $bad['db'][] = $ill;
                continue;
            } else {
                $good[] = $ill;
            }
        }

        $total = count($ills);

        if (!empty($good)) {
            $goodOut = "Successful: " . count($good) . "/$total\n\n";
            $goodOut .= "[id] - [name]\n";

            foreach ($good as $ill) {
                $goodOut .=
                    $ill['id'] . " - " . $ill['name'] . "\n";
            }

            $this->flash('success', $goodOut);
        }


        if (!empty($bad)) {
            $badCount = 0;
            foreach ($bad as $reason) {
                foreach ($reason as $ills) {
                    $badCount++;
                }
            }

            $badOut = "Failed: " . $badCount . "/$total\n";

            foreach ($bad as $reason => $ills) {
                switch ($reason) {

                    case 'data' :
                        $badOut .= "\nBad input data:\n";
                        $badOut .= "[name]\n";
                        foreach ($ills as $ill) {
                            $badOut .= $ill['name'] . "\n";
                        }
                        break;

                    case 'duplicate' :
                        $badOut .= "\nDuplicates:\n";
                        $badOut .= "[name]\n";
                        foreach ($ills as $ill) {
                            $badOut .= $ill['name'] . "\n";
                        }
                        break;

                    case 'db' :
                        $badOut .= "\nDatabase failure:\n";
                        $badOut .= "[name]\n";
                        foreach ($ills as $ill) {
                            $badOut .= $ill['name'] . "\n";
                        }
                        break;
                }
            }

            $this->flash('warning', $badOut);
        }

        return Router::redirect('/admin/illnesses');
    }
}
