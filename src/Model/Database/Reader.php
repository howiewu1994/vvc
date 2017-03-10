<?php
namespace VVC\Model\Database;

use VVC\Model\Data\Drug;
use VVC\Model\Data\IllnessCollection;
use VVC\Model\Data\IllnessRecord;
use VVC\Model\Data\Payment;
use VVC\Model\Data\Stay;
use VVC\Model\Data\Step;
use VVC\Model\Data\TherapyStep;
use VVC\Model\Data\User;

/**
 * Processes SELECT queries
 */
class Reader extends Connection
{
    /**
     * Returns existing user data
     * @param  string $username
     * @return User OR false
     */
    public function findUserByUsername(string $username)
    {
        // test stub
        if (NO_DATABASE) {
            return $this->findUserByUsername_stub($username);
        }

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return false;
        }

        return new User(
            $result['id'],
            $result['username'],
            $result['password'],
            $result['role_id'],
            $result['created_at']
        );
    }

    /**
     * Returns existing user data
     * @param  int $userId
     * @return User OR false
     */
    public function findUserById(int $userId)
    {
        // test stub
        if (NO_DATABASE) {
            return false;
        }

        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return new User(
            $result['id'],
            $result['username'],
            $result['password'],
            $result['role_id'],
            $result['created_at']
        );
    }

    /**
     * Returns array with all users, full details
     * @return array of Users, empty or not
     */
    public function getAllUsers() : array
    {
        if (NO_DATABASE) {
            return [];
        }

        // TODO
        $users = [];

        return $users;
    }

    /**
     * Returns all illnesses collection
     * Only basic info is needed, no steps array
     * @return IllnessCollection, empty or not
     */
    public function getAllIllnesses() : IllnessCollection
    {
        if (NO_DATABASE) {
            $collection = new IllnessCollection();
            $collection->addRecord(new IllnessRecord(
                1,
                'Illness 1',
                'Class 1',
                'Illness 1 description.'
            ));
            $collection->addRecord(new IllnessRecord(
                2,
                'Illness 2',
                'Class 1',
                'Illness 2 description.'
            ));
            $collection->addRecord(new IllnessRecord(
                3,
                'Illness 3',
                'Class 2',
                'Illness 3 description.'
            ));

            return $collection;
        }

        $sql = "SELECT * FROM illnesses";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $collection = new IllnessCollection();

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            $collection->addRecord(new IllnessRecord(
                // TODO
                $row['id'],
                $row['name'],
                $row['class'],
                $row['description']
            ));
        }

        return $collection;
    }

    /**
     * Returns full illness details,
     * including all steps and details of each step
     * @param  int  $id     - illness id
     * @return IllnessRecord OR false if not found
     */
    public function getFullIllnessById(int $id)
    {
        if (NO_DATABASE) {
            if ($id <= 3) {    // just for tests
                $illness = new IllnessRecord(
                    $id,
                    "Illness $id",
                    'Class ' . ($id < 3) ? 1 : 2,
                    "Illness $id description."
                );

                $steps = $this->getStepsByIllnessId($id);
                $illness->addSteps($steps);

                return $illness;
            } else {
                return false;
            }
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $illness = new IllnessRecord(
            // TODO
            // $result[''], ..
        );

        $steps = getStepsByIllnessId($id);
        $illness->addSteps($steps);

        return $illness;
    }

    /**
     * Returns all illness steps with full details
     * @param  int $illnessId
     * @return array of steps, empty or not
     */
    public function getStepsByIllnessId(int $illnessId) : array
    {
        if (NO_DATABASE) {
            $steps = [];
            $steps[] = new Step(1, '接诊');
            $steps[] = new Step(2, '检查');;
            $steps[] = new Step(3, '诊断');
            $steps[] = new TherapyStep(4, '治疗方案');

            foreach ($steps as $step) {
                $stepNum = $step->getNum();

                $text = $this->getStepText($illnessId, $stepNum);
                $step->setText($text);

                $pictures = $this->getStepPictures($illnessId, $stepNum);
                $step->addPictures($pictures);

                $videos = $this->getStepVideos($illnessId, $stepNum);
                $step->addVideos($videos);

                if ($step instanceof TherapyStep) {
                    $drugs = $this->getDrugsByIllnessId($illnessId);
                    $step->addDrugs($drugs);

                    $payments = $this->getPaymentsByIllnessId($illnessId);
                    $step->addPayments($payments);

                    $days = $this->getStayByIllnessId($illnessId);
                    if ($days > 0) {
                        $step->setStay(new Stay($days));
                    }
                }
            }

            return $steps;
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);

        $steps = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            $steps[] = new Step(
                // TODO
                //$row[''], ..
            );
        }

        foreach ($steps as $step) {
            $stepNum = $step->getNum();

            $text = $this->getStepText($illnessId, $stepNum);
            $step->setText($text);

            $pictures = $this->getStepPictures($illnessId, $stepNum);
            $step->addPictures($pictures);

            $videos = $this->getStepVideos($illnessId, $stepNum);
            $step->addVideos($videos);

            if ($step instanceof TherapyStep) {
                $drugs = $this->getDrugsByIllnessId($illnessId);
                $step->addDrugs($drugs);

                $payments = $this->getPaymentsByIllnessId($illnessId);
                $step->addPayments($payments);

                $days = $this->getStayByIllnessId($illnessId);
                if ($days > 0) {
                    $step->setStay(new Stay($days));
                }
            }
        }

        return $steps;
    }

    /**
     * Returns step text
     * @param  int $illnessId
     * @param  int $stepNum
     * @return string
     */
    public function getStepText(int $illnessId, int $stepNum) : string
    {
        if (NO_DATABASE) {
            return "Description of step $stepNum for illness $illnessId";;
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $stepNum]);

        return $stmt->fetchColumn(0);
    }

    /**
     * Returns all step pictures, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of pictures, empty or not
     */
    public function getStepPictures(int $illnessId, int $stepNum) : array
    {
        if (NO_DATABASE) {
            return ["/img/step$stepNum.png"];
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $stepNum]);

        $pics = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            // TODO
            //$pics[] = $row[''];
        }

        return $pics;
    }

    /**
     * Returns all step videos, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of videos, empty or not
     */
    public function getStepVideos(int $illnessId, int $stepNum) : array
    {
        if (NO_DATABASE) {
            return [];
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $stepNum]);

        $vids = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            // TODO
            //$vids[] = $row[''];
        }

        return $vids;
    }

    /**
     * Returs all drugs associated with illness, if any
     * @param  int  $illnessId
     * @return array of drugs, empty or not
     */
    public function getDrugsByIllnessId(int $illnessId) : array
    {
        if (NO_DATABASE) {
            return [new Drug(
                "AB-" . rand(1,9) . rand(1,9),
                'Drug',
                'Drug description',
                '/img/drug.jpg',
                rand(30, 100)
            )];
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);

        $drugs = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            // TODO
            //$drugs[] = $row[''];
        }

        return $drugs;
    }

    /**
     * Returs all payments associated with illness, if any
     * @param  int  $illnessId
     * @return array of payments, empty or not
     */
    public function getPaymentsByIllnessId(int $illnessId) : array
    {
        if (NO_DATABASE) {
            return [new Payment(
                1, 'Payment for something', rand(25, 50)
            )];
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);

        $payments = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            // TODO
            //$payments[] = $row[''];
        }

        return $payments;
    }

    /**
     * Returs number of days for staying at clinic for the illness
     * @param  int  $illnessId
     * @return int  days OR false
     */
    public function getStayByIllnessId(int $illnessId)
    {
        if (NO_DATABASE) {
            return rand(0, 5);
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);

        return $stmt->fetchColumn(0);
    }

    /**
     * Returns array with all drugs, full details
     * @return array of Drugs, empty or not
     */
    public function getAllDrugs() : array
    {
        if (NO_DATABASE) {
            return [];
        }

        // TODO
        $drugs = [];

        return $drugs;
    }

    /**
     * Returns all illnesses associated with the drug
     * @param  string $drugId
     * @return array of IllnessRecords OR false
     */
    public function findIllnessesByDrugId(string $drugId)
    {
        if (NO_DATABASE) {
            return false;
        }

        // TODO
    }

    /**
     * Returns array with all payments, full details
     * @return array of Payments, empty or not
     */
    public function getAllPayments() : array
    {
        if (NO_DATABASE) {
            return [];
        }

        // TODO
        $payments = [];

        return $payments;
    }


    public function findUserByUsername_stub($username)
    {
        if ($username == ADMIN_NAME){
            return new User(
                1,
                ADMIN_NAME,
                password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT),
                1,
                date("Y-m-d H:i:s")
            );
        } elseif ($username == USER_NAME) {
            return new User(
                2,
                USER_NAME,
                password_hash(USER_PASSWORD, PASSWORD_DEFAULT),
                2,
                date("Y-m-d H:i:s")
            );
        } else {
            return false;
        }
    }
}
