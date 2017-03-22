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
        $sql = "UPDATE users SET password = ?
                WHERE user_id = ?";
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
        $sql = "UPDATE users
        		SET user_name=?,
                    password=?,
                    role_id=?,
                    createdAt=?
                WHERE user_id=? ";
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
        $sql = "UPDATE illness
        		SET ill_name=?,
                    class_name=?,
                    ill_describe=?
                WHERE ill_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name,$class,$description]);
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
        $sql = "UPDATE stepname
        		SET step_name=?
                WHERE step_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
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
        $sql = "UPDATE drug
        		SET drug_name=?,
                    drug_text=?,
                    drug_picture=?,
                    drug_cost=?
                WHERE drug_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name,$text,$picture,$cost,$id]);
    }

    /**
     * Rewrites payment details based on id,
     * this action affects all corresponding illness records
     * @param  int    $id     - not updated
     * @param  int    $illnessId
     * @param  string $name
     * @param  float  $cost
     * @param  int    $number
     * @return void
     */
    public function updatePayment(
        int $id, int $illnessId, string $name, float $cost,int $number
    ) {
        $sql = "UPDATE payments
        		SET ill_id=?,
        		    pay_name=?,
        		    pay_cost=?
        		    number=?
        		WHERE pay_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$name,$cost,$number,$id]);
    }

    /**
     * Rewrites number of days for hospitalization based on illness id
     * @param  int  $illnessId  - not updated
     * @param  int  $days
     * @return void
     */
    public function updateStay(int $illnessId, int $days) : void
    {
        $sql = "UPDATE payments
        		SET number=?
                WHERE pay_name='stay' AND ill_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
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
        $sql = "UPDATE steps
        		SET step_text=?
                WHERE step_num=? AND ill_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$text]);
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
     * @param  string $newpath
     * @param  string $oldpath
     * @return true if successful OR false if rolled back
     */
    public function updatePicture(string $newpath, string $oldpath) : bool
    {
        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Update for illnesses
            $sql = "UPDATE illpic
            		SET pic_path='?'
                    WHERE pic_path=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$newpath, $oldpath]);

            // Update for drugs
            $sql = "UPDATE drug
            		SET drug_picture=?
                    WHERE drug_picture=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$newpath, $oldpath]);

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            Logger::log(
                'db', 'error',
                "Failed to update picture $oldpath to $newpath, rolled back transaction",
                $e
            );
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Updates picture for all steps
     * This action is provoked because of changing real picture on file system
     * @param  string $newpath
     * @param  string $oldpath
     * @return void
     */
    public function updateVideo(string $newpath, string $oldpath) : void
    {
        $sql = "UPDATE illvid
        		SET vid_path=?
                WHERE vid_path=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$newpath, $oldpath]);
    }

}
