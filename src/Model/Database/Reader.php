<?php
namespace VVC\Model\Database;

use VVC\Model\Data\Drug;
use VVC\Model\Data\IllnessCollection;
use VVC\Model\Data\IllnessRecord;
use VVC\Model\Data\Payment;
use VVC\Model\Data\TreatmentStep;
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
    public function findUserByUsername($username)
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
    public function findUserById($userId)
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
                'Illness 1 description.',
                2
            ));
            $collection->addRecord(new IllnessRecord(
                2,
                'Illness 2',
                'Class 1',
                'Illness 2 description.',
                0
            ));
            $collection->addRecord(new IllnessRecord(
                3,
                'Illness 3',
                'Class 2',
                'Illness 3 description.',
                1
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
                $row['description'],
                $row['stay']    // hospitalization
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
    public function getFullIllnessById($id)
    {
        if (NO_DATABASE) {
            if ($id <= 3) {    // just for tests
                $illness = new IllnessRecord(
                    $id,
                    "Illness $id",
                    'Class 1',
                    "Illness $id description.",
                    rand(0, 14)
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
    public function getStepsByIllnessId($illnessId) : array
    {
        if (NO_DATABASE) {
            $steps = [];
            $steps[] = new TreatmentStep(
                1,
                'Step One',
                'First...'
            );
            $steps[] = new TreatmentStep(
                2,
                'Step Two',
                'Second...'
            );
            $steps[] = new TreatmentStep(
                3,
                'Step Three',
                'Third...'
            );

            foreach ($steps as $step) {
                $stepNum = $step->getSeqNum();

                $pictures = $this->getStepPictures($illnessId, $stepNum);
                $step->addPictures($pictures);

                $videos = $this->getStepVideos($illnessId, $stepNum);
                $step->addVideos($videos);

                $drugs = $this->getStepDrugs($illnessId, $stepNum);
                $step->addDrugs($drugs);

                $payments = $this->getStepPayments($illnessId, $stepNum);
                $step->addPayments($payments);
            }

            return $steps;
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);

        $steps = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            $steps[] = new TreatmentStep(
                // TODO
                //$row[''], ..
            );
        }

        foreach ($steps as $step) {
            $stepNum = $step->getSeqNum();

            $pictures = $this->getStepPictures($illnessId, $stepNum);
            $step->addPictures($pictures);

            $videos = $this->getStepVideos($illnessId, $stepNum);
            $step->addVideos($videos);

            $drugs = $this->getStepDrugs($illnessId, $stepNum);
            $step->addDrugs($drugs);

            $payments = $this->getStepPayments($illnessId, $stepNum);
            $step->addPayments($payments);
        }

        return $steps;
    }

    /**
     * Returns all step pictures, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of pictures, empty or not
     */
    public function getStepPictures($illnessId, $stepNum) : array
    {
        if (NO_DATABASE) {
            return ["/img/step$stepNum.jpg"];
        }

        $sql = "";  // TODO
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $stepNum]);

        $pics = [];

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            // TODO
            //$pics[] = $row[''];
        }

        return pics;
    }

    /**
     * Returns all step videos, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of videos, empty or not
     */
    public function getStepVideos($illnessId, $stepNum) : array
    {
        if (NO_DATABASE) {
            return [];
        }

        // TODO
        // SQL
        $vids = [];

        return $vids;
    }

    /**
     * Returs all step drugs, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of drugs, empty or not
     */
    public function getStepDrugs($illnessId, $stepNum) : array
    {
        if (NO_DATABASE) {
            return [new Drug(
                "AB-$illnessId" . rand(0,2) . "-$stepNum" . rand(5,10),
                'Drug',
                'Drug description',
                '/img/drug.jpg',
                rand(30, 100)
            )];
        }

        // TODO
        // SQL
        $drugs = [];

        return $drugs;
    }

    /**
     * Returs all step payments, if any
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return array of payments, empty or not
     */
    public function getStepPayments($illnessId, $stepNum) : array
    {
        if (NO_DATABASE) {
            return [new Payment(
                1, 'Payment for something', rand(25, 50)
            )];
        }

        // TODO
        // SQL
        $payments = [];

        return $payments;
    }

    // Don't need for now:
    //
    // getClassByName
    // getAllClasses

    // getDrugById ?
    // getAllDrugs

    // getPaymentById ?
    // getAllPayments

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
