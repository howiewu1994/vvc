<?php
namespace VVC\Model\Database;

/**
 * Processes UPDATE queries
 */
class Updater extends Connection
{
    /**
     * Changes user password based on user id
     * @param  int $userId
     * @param  string $password - already hashed
     * @return void
     */
    public function changePassword(int $userId, string $password) : void
    {
        $sql = "UPDATE users SET password = ? where id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$password, $userId]);
    }

    /**
     * Rewrites user details based on id
     * @param  int    $id       - not updated
     * @param  string $username
     * @param  string $password
     * @param  int    $roleId
     * @param  string $createdAt
     * @return void
     */
    public function updateUser(
        int     $id,
        string  $username,
        string  $password,
        int     $roleId,
        string  $createdAt
    ) : void
    {
        $sql = "UPDATE users SET
            username = ?, password = ?, role_id = ?, created_at = ?
            WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $password, $roleId, $createdAt, $id]);
    }

    /**
     * Rewrites BASIC illness info based on id
     * @param  int    $id          - not updated
     * @param  string $name
     * @param  string $class
     * @param  string $description
     * @return void
     */
    public function updateIllness(
        int     $id,
        string  $name,
        string  $class,
        string  $description
    ) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Rewrites step name based on step id (num)
     * this action affects all illnesses (step name change is universal)
     * @param  int    $num  - not updated
     * @param  string $name
     * @return void
     */
    public function updateStep(int $num, string $name) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Rewrites drug details based on id,
     * this action affects all corresponding illness records
     * @param  string $id      - not updated
     * @param  string $name
     * @param  string $text
     * @param  string $picture
     * @param  float  $cost
     * @return void
     */
    public function updateDrug(
        string  $id,
        string  $name,
        string  $text,
        string  $picture,
        float   $cost
    ) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Rewrites payment details based on id,
     * this action affects all corresponding illness records
     * @param  int    $id     - not updated
     * @param  string $name
     * @param  float  $amount
     * @return void
     */
    public function updatePayment(int $id, string $name, float $amount) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Rewrites number of days for hospitalization based on illness id
     * @param  int  $illnessId  - not updated
     * @param  int  $days
     * @return void
     */
    public function updateStay(int $illnessId, int $days) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Rewrites step text based on illness id and step id (num)
     * @param  int    $illnessId - not updated
     * @param  int    $stepNum   - not updated
     * @param  string $text
     * @return void
     */
    public function updateStepText(
        int     $illnessId,
        int     $stepNum,
        string  $text
    ) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * Replaces all current step pictures with new vids array
     * @param  int    $illnessId    - not updated
     * @param  int    $stepNum      - not updated
     * @param  array  $pics         - may consist of only one picture
     * @return void
     */
    public function updateStepPictures(
        int   $illnessId,
        int   $stepNum,
        array $pics
    ) : void
    {
        (new Deleter())->removeAllPicturesFromStep($illnessId, $stepNum);

        foreach ($pics as $pic) {
            (new Creator())->addPictureToStep($illnessId, $stepNum, $pic);
        }
    }

    /**
     * Replaces all current step videos with new vids array
     * @param  int    $illnessId    - not updated
     * @param  int    $stepNum      - not updated
     * @param  array  $vids         - may consist of only one picture
     * @return void
     */
    public function updateStepVideos(
        int   $illnessId,
        int   $stepNum,
        array $vids
    ) : void
    {
        (new Deleter())->removeAllVideosFromStep($illnessId, $stepNum);

        foreach ($vids as $vid) {
            (new Creator())->addVideoToStep($illnessId, $stepNum, $vid);
        }
    }

    /**
     * Replaces all current illness drugs with new drugs array
     * @param  int    $illnessId - not updated
     * @param  array  $drugs     - contains new drug IDs
     * @return void
     */
    public function updateIllnessDrugs(int $illnessId, string $vid) : void
    {
        (new Deleter())->removeAllDrugsFromIllness($illnessId);

        foreach ($drugs as $drugId) {
            (new Creator())->addDrugToIllness($illnessId, $drugId);
        }
    }

    /**
     * Replaces all current illness payments with new payments array
     * @param  int    $illnessId - not updated
     * @param  array  $payments  - contains new payments IDs
     * @return void
     */
    public function updateIllnessPayments(int $illnessId, array $payments) : void
    {
        (new Deleter())->removeAllPaymentsFromIllness($illnessId);

        foreach ($payments as $paymentId) {
            (new Creator())->addPaymentToIllness($illnessId, $paymentId);
        }
    }

    /**
     * Updates picture for all steps AND for all drugs
     * This action is provoked because of changing real picture on file system
     *
     * If any of updates fail, roll back transaction
     *
     * @param  string $path
     * @return true if successful OR false if rolled back
     */
    public function updatePicture(string $path) : bool
    {
        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Update for illnesses
            $sql = "UPDATE ill_pic";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$path]);

            // Update for drugs
            $sql = "UPDATE drug_pic";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$path]);

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            // TODO logError $e
            // If any step fails, roll back
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Updates picture for all steps
     * This action is provoked because of changing real picture on file system
     * @param  string $path
     * @return void
     */
    public function updateVideo(string $path) : void
    {
        $sql = "UPDATE ill_pic";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$path]);
    }

}
