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
        $sql = "UPDATE users SET password = '$password' 
                WHERE user_id = '$userId'";
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
        		SET user_name='$username',
                    password='$password',
                    role_id='$roleId',
                    createdAt='$createdAt'
                WHERE user_id='$id' ";
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
        		SET ill_name='$name',
                    class_name='$class',
                    ill_describe='$description'
                WHERE ill_id='$id' ";
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
        		SET step_name='$name'
                WHERE step_id='$num' ";
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
        		SET drug_name='$name',
                    drug_text='$text',
                    drug_picture='$picture',
                    drug_cost='$cost'
                WHERE drug_id='$id' ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name,$text,$picture,$cost]);
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
    public function updatePayment(int $id, int $illnessId, string $name, float $cost,int $number) : void
    {
        $sql = "UPDATE payments 
        		SET ill_id='$illnessId', 
        		    pay_name='$name',       		    
        		    pay_cost='$cost'
        		    number='$number'
        		WHERE pay_id='$id' ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$name,$cost,$number]);
    }

    /**
     * Rewrites number of days for hospitalization based on illness id
     * @param  int  $illnessId  - not updated
     * @param  int  $days
     * @return void
     */
   /* public function updateStay(int $illnessId, int $days) : void
    {
        $sql = "UPDATE ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([]);
    }*/

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
        		SET step_text='$text'
                WHERE step_num='$stepNum' AND ill_id='$illnessId' ";
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
