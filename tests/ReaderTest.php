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
            $expected[] = new Payment(
                $entry['step_num'],
                $entry['step_name']
            );
        }

        $dbReader = new Reader($this->db);

        $result = $dbReader->findIllnessSteps();
        $this->assertEquals($expected, $result);
    }

}
