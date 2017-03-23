<?php
namespace VVC\Test;

require_once __DIR__ . '/../web/config.php';

// use VVC\Model\Database\Creator;
// use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
// use VVC\Model\Database\Updater;

use VVC\Model\Data\User;

class ReaderTest extends DBTest
{
    /**
     * @test
     */
    public function findUserByUsername_Test()
    {
        // Expect User
        foreach ($this->data['users'] as $user) {
            $input[] = $user['user_name'];
            $expected[] = new User(
                $user['user_id'],
                $user['user_name'],
                $user['password'],
                $user['role_id'],
                $user['createdAt']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $user = $dbReader->findUserByUsername($input[$i]);
            $this->assertEquals($expected[$i], $user);
        }
    }

    /**
     * @test
     */
    public function findUserById_Test()
    {
        // Expect User
        foreach ($this->data['users'] as $user) {
            $input[] = $user['user_id'];
            $expected[] = new User(
                $user['user_id'],
                $user['user_name'],
                $user['password'],
                $user['role_id'],
                $user['createdAt']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $user = $dbReader->findUserById($input[$i]);
            $this->assertEquals($expected[$i], $user);
        }
    }

    /**
     * @test
     */
    public function getAllUsers_Test()
    {
        // Expect array
        foreach ($this->data['users'] as $user) {
            $expected[] = new User(
                $user['user_id'],
                $user['user_name'],
                $user['password'],
                $user['role_id'],
                $user['createdAt']
            );
        }

        $dbReader = new Reader($this->db);

        $user = $dbReader->getAllUsers();
        $this->assertEquals($expected, $user);
    }

    /**
     * @test
     */
    public function getAllIllnesses_Test()
    {
        // Expect array
        foreach ($this->data['illness'] as $entry) {
            $expected[] = new IllnessRecord(
                $entry['ill_id'],
                $entry['ill_name'],
                $entry['class_name'],
                $entry['ill_describe']
            );
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getAllIllnesses();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function getAllDrugs_Test()
    {
        // Expect array
        foreach ($this->data['drug'] as $entry) {
            $expected[] = new Drug(
                $entry['drug_id'],
                $entry['drug_name'],
                $entry['drug_text'],
                $entry['drug_picture'],
                $entry['drug_cost']
            );
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getAllDrugs();
        $this->assertEquals($expected, $result);
    }

    public function getAllPayments_Test()
    {
        // Expect array
        foreach ($this->data['payments'] as $entry) {
            $expected[] = new Payment(
                $entry['pay_id'],
                $entry['ill_id'],
                $entry['pay_name'],
                $entry['pay_cost'],
                $entry['number']
            );
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getAllPayments();
        $this->assertEquals($expected, $result);
    }

    public function getFullIllnessById_Test()
    {
        // Expect IllnessRecord
        $entry = $this->data['illnesses'][0];
        $input[] = $entry['ill_id'];

        $new = new IllnessRecord(
            $entry['ill_id'],
            $entry['ill_name'],
            $entry['class_name'],
            $entry['ill_describe']
        );

        $step1 = new Step(1, '');
        $step1->setText('');
        $step1->addPictures(['']);
        $step1->addVideos(['']);
        $new->addSteps([$step1, $step2, $step3, $step4]);

        $drug1 = new Drug(

        );
        $new->addDrugs([$drug1]);

        $pay1 = new Payment(

        );
        $new->addPayments([$pay1]);

        $expected[] = $new;

        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $result = $dbReader->getFullIllnessById($input[$i]);
            $this->assertEquals($expected[$i], $result);
        }
    }

    public function getStepsByIllnessId_Test()
    {
        // Expect array
        $input = 1;
        $step1 = new Step('');
        $step1->setText('');
        $step1->addPictures(['']);
        $step1->addVideos(['']);

        $step2 = new Step('');
        $step2->setText('');
        $step2->addPictures(['']);
        $step2->addVideos(['']);

        $step3 = new Step('');
        $step3->setText('');
        $step3->addPictures(['']);
        $step3->addVideos(['']);

        $step4 = new Step('');
        $step4->setText('');
        $step4->addPictures(['']);
        $step4->addVideos(['']);

        $expected = [$step1, $step2, $step3, $step4];

        $dbReader = new Reader($this->db);

        $result = $dbReader->getStepsByIllnessId($input);
        $this->assertEquals($expected, $result);
    }

    public function findIllnessSteps_Test()
    {
        // Expect array
        foreach ($this->data['stepname'] as $entry) {
            $illnessId = $entry['ill_id'];
            $input[] = $illnessId;
            $expected[$illnessId][] = new Step(
                $entry['step_num'],
                $entry['step_name']
            );
        }

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $illnessId = $input[$i];
            $result = $dbReader->findIllnessSteps($illnessId);
            $this->assertEquals($expected[$illnessId][$i], $result);
        }
    }

    public function getStepText_TestCase()
    {
        $testCase = [];
        foreach ($this->data['steps'] as $entry)) {
            if (!in_array([$entry['ill_id'], $entry['step_num']], $testCase)) {
                $testCase[] = [$entry['ill_id'], $entry['step_num']];
            }
        }

        foreach ($testCase as $tc) {
            $this->getStepVideos_Test($tc[0], $tc[1]);
        }
    }

    public function getStepText_Test(int $illnessId, int $stepNum)
    {
        // Expect array
        foreach ($this->data['steps'] as $entry) {
            if ($entry['ill_id'] == $illnessId &&
                $entry['step_num'] == $stepNum)
            {
                $expected[] = $entry['step_text'];
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getStepText($illnessId, $stepNum);
        $this->assertEquals($expected, $result);
    }

    public function getStepPictures_TestCase()
    {
        $testCase = [];
        foreach ($this->data['illpic'] as $entry)) {
            if (!in_array([$entry['ill_id'], $entry['step_num']], $testCase)) {
                $testCase[] = [$entry['ill_id'], $entry['step_num']];
            }
        }

        foreach ($testCase as $tc) {
            $this->getStepVideos_Test($tc[0], $tc[1]);
        }
    }

    public function getStepPictures_Test(int $illnessId, int $stepNum)
    {
        // Expect array
        foreach ($this->data['illpic'] as $entry) {
            if ($entry['ill_id'] == $illnessId &&
                $entry['step_num'] == $stepNum)
            {
                $expected[] = $entry['pic_path'];
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getStepPictures($illnessId, $stepNum);
        $this->assertEquals($expected, $result);
    }

    public function getStepVideos_TestCase()
    {
        $testCase = [];
        foreach ($this->data['illvid'] as $entry)) {
            if (!in_array([$entry['ill_id'], $entry['step_num']], $testCase)) {
                $testCase[] = [$entry['ill_id'], $entry['step_num']];
            }
        }

        foreach ($testCase as $tc) {
            $this->getStepVideos_Test($tc[0], $tc[1]);
        }
    }

    public function getStepVideos_Test(int $illnessId, int $stepNum)
    {
        // Expect array
        foreach ($this->data['illvid'] as $entry) {
            if ($entry['ill_id'] == $illnessId &&
                $entry['step_num'] == $stepNum)
            {
                $expected[] = $entry['vid_path'];
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getStepVideos($illnessId, $stepNum);
        $this->assertEquals($expected, $result);
    }

    public function getDrugsByIllnessId_TestCase()
    {
        $testCase = [];
        foreach ($this->data['illdrug'] as $entry)) {
            if (!in_array($entry['ill_id'], $testCase)) {
                $testCase[] = $entry['ill_id'];
            }
        }

        foreach ($testCase as $illnessId) {
            $this->getDrugsByIllnessId_Test($illnessId);
        }
    }

    public function getDrugsByIllnessId_Test(int $illnessId)
    {
        $set = [];
        foreach ($this->data['illdrug'] as $entry) {
            if ($entry['ill_id'] == $illnessId) {
                $set[] = $entry['drug_id'];
            }
        }

        // Expect array
        foreach ($this->data['drugs'] as $entry) {
            if (in_array($entry['drug_id']), $set) {
                $expected[] = new Drug(
                    $entry['drug_id'],
                    $entry['drug_name'],
                    $entry['drug_text'],
                    $entry['drug_picture'],
                    $entry['drug_cost']
                );
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getDrugsByIllnessId($illnessId);
        $this->assertEquals($expected, $result);
    }

    public function getPaymentsByIllnessId_TestCase()
    {
        $testCase = [];
        foreach ($this->data['payments'] as $entry)) {
            if (!in_array($entry['ill_id'], $testCase)) {
                $testCase[] = $entry['ill_id'];
            }
        }

        foreach ($testCase as $illnessId) {
            $this->getPaymentsByIllnessId_Test($illnessId);
        }
    }

    public function getPaymentsByIllnessId_Test(int $illnessId)
    {
        // Expect array
        foreach ($this->data['payments'] as $entry) {
            if ($entry['ill_id'] == $illnessId) {
                $expected[] = new Payment(
                    $entry['pay_id'],
                    $entry['ill_id'],
                    $entry['pay_name'],
                    $entry['pay_cost'],
                    $entry['number']
                );
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->getPaymentsByIllnessId($illnessId);
        $this->assertEquals($expected, $result);
    }

    public function getStayByIllnessId_Test()
    {
        // Expect array
        $illnessId = 1;

        $pay1 = new Payment(1);
        $expected = [$pay1];

        $dbReader = new Reader($this->db);

        $result = $dbReader->getPaymentsByIllnessId($illnessId);
        $this->assertEquals($expected, $result);
    }

    public function findIllnessesByDrugId_TestCase()
    {
        $testCase = [];
        foreach ($this->data['illdrug'] as $entry)) {
            if (!in_array($entry['drug_id'], $testCase)) {
                $testCase[] = $entry['drug_id'];
            }
        }

        foreach ($testCase as $drugId) {
            $this->findIllnessesByDrugId_Test($drugId);
        }
    }

    public function findIllnessesByDrugId_Test(int $drugId)
    {
        $set = [];
        foreach ($this->data['illdrug'] as $entry) {
            if ($entry['drug_id'] == $drugId) {
                $set[] = $entry['ill_id'];
            }
        }

        // Expect array
        foreach ($this->data['illness'] as $entry) {
            if (in_array($entry['ill_id']), $set) {
                $expected[] = new IllnessRecord(
                    $entry['ill_id'],
                    $entry['ill_name'],
                    $entry['class_name'],
                    $entry['ill_describe']
                );
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->findIllnessesByDrugId($drugId);
        $this->assertEquals($expected, $result);
    }

    public function findIllnessesByPaymentId_TestCase()
    {
        $testCase = [];
        foreach ($this->data['payments'] as $entry)) {
            if (!in_array($entry['pay_id'], $testCase)) {
                $testCase[] = $entry['pay_id'];
            }
        }

        foreach ($testCase as $paymentId) {
            $this->findIllnessesByPaymentId_Test($paymentId);
        }
    }

    public function findIllnessesByPaymentId_Test(int $paymentId)
    {
        // Expect array
        foreach ($this->data['payments'] as $entry) {
            if ($entry['pay_id'] == $paymentId) {
                $expected[] = new IllnessRecord(
                    $entry['ill_id'],
                    $entry['ill_name'],
                    $entry['class_name'],
                    $entry['ill_describe']
                );
            }
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->findIllnessesByPaymentId($paymentId);
        $this->assertEquals($expected, $result);
    }

    public function findIllnessById_Test()
    {
        // Expect IllnessRecord
        foreach ($this->data['illness'] as $entry) {
            $input[] = $entry['ill_id'];
            $expected[] = new IllnessRecord(
                $entry['ill_id'],
                $entry['ill_name'],
                $entry['class_name'],
                $entry['ill_describe']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $result = $dbReader->findIllnessById($input[$i]);
            $this->assertEquals($expected[$i], $result);
        }
    }

    public function findIllnessByName_Test()
    {
        // Expect IllnessRecord
        foreach ($this->data['illness'] as $entry) {
            $input[] = $entry['ill_name'];
            $expected[] = new IllnessRecord(
                $entry['ill_id'],
                $entry['ill_name'],
                $entry['class_name'],
                $entry['ill_describe']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $result = $dbReader->findIllnessByName($input[$i]);
            $this->assertEquals($expected[$i], $result);
        }
    }

    public function findDrugById_Test()
    {
        // Expect Drug
        foreach ($this->data['drug'] as $entry) {
            $input[] = $entry['drug_id'];
            $expected[] = new Drug(
                $entry['drug_id'],
                $entry['drug_name'],
                $entry['drug_text'],
                $entry['drug_picture'],
                $entry['drug_cost']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $result = $dbReader->findDrugById($input[$i]);
            $this->assertEquals($expected[$i], $result);
        }
    }

    public function findDrugByName_Test()
    {
        // Expect Drug
        foreach ($this->data['drug'] as $entry) {
            $input[] = $entry['drug_name'];
            $expected[] = new Drug(
                $entry['drug_id'],
                $entry['drug_name'],
                $entry['drug_text'],
                $entry['drug_picture'],
                $entry['drug_cost']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $result = $dbReader->findDrugByName($input[$i]);
            $this->assertEquals($expected[$i], $result);
        }
    }

}
