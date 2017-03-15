<?php
namespace VVC\Model\Database;

use VVC\Model\Data\User;

/**
 * Processes INSERT queries
 */
class Creator extends Connection
{
    /**
     * Creates a new user and returns back complete user details
     * @param  string  $username
     * @param  string  $password   - already hashed
     * @param  integer $role_id    - admin = 1, user = 2
     * @param  string  $created_at - date of registration
     * @return User OR false
     */
    public function createUser(
        string  $username,
        string  $password = null,
                $roleId = 2,
        string  $createdAt = null
    ) {
        // test stub
        if (NO_DATABASE) {
            return $this->createUser_stub($username, $password);
        }

        if ($password == null) {
            $password = password_hash(BATCH_USER_PASSWORD, PASSWORD_DEFAULT);
        }

        if ($createdAt == null) {
            $createdAt = date("Y-m-d H:i:s");
        }

        $sql = "INSERT INTO
            users(user_name, password, role_id, createdAt)
            VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $password, $roleId, $createdAt]);

        return (new Reader())->findUserByUsername($username);
    }

    /**
     * Creates new illness record in the database,
     * does NOT create any steps
     * @param  string $name
     * @param  string $class
     * @param  string $description
     * @return int  - new illness id
     */
    public function createIllness(
        string  $name,
        string  $class,
        string  $description
    ) : int
    {
        $sql = "INSERT INTO illness(ill_name,class_name,ill_describe)
                VALUES(?,?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name,$class,$description]);

        return $this->db->lastInsertId();
    }

    /**
     * Creates new step template
     * @param  int    $num  - id and sequential number of a step
     * @param  string $name
     * @return void
     */
    public function createStep(int $num, string $name) : void
    {
        $sql = "INSERT INTO stepname(step_num,step_name)
        		VALUES (?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$num,$name]);
    }

    /**
     * Creates new drug
     * @param  string $name
     * @param  string $text
     * @param  string $picture
     * @param  float  $cost
     * @return int  - new drug id
     */
    public function createDrug(
        string  $name,
        string  $text,
        string  $picture,
        float   $cost
    ) : int
    {
        $sql = "INSERT INTO drug(drug_name,drug_text,drug_picture,drug_cost)
        		VALUES(?,?,?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name,$text,$picture,$cost]);

        return $this->db->lastInsertId();
    }

    /**
     * Creates new payment
     * @param  int    $illnessId
     * @param  string $name
     * @param  float  $cost
     * @param  int    $num
     * @return int  - new payment id
     */
    public function createPayment(
        int $illnessId, string $name, float $cost, int $num
    ) : int
    {
        $sql = "INSERT INTO payments(ill_id,pay_name,pay_cost,number)
        		VALUES(?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$name,$cost,$num]);

        return $this->db->lastInsertId();
    }

    /**
     * Adds a new stay entry based on illness id
     * @param  int  $illnessId
     * @param  int  $days
     * @return void
     */
   /* public function addStay(int $illnessId, int $days) : void
    {
        $sql = "INSERT INTO ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([]);
    }
    */

    /**
     * Adds text to some illness step
     * @param  int    $illnessId
     * @param  int    $stepNum
     * @param  string $text
     * @return void
     */
    public function addTextToStep(
        int $illnessId, int $stepNum, string $text
    ) : void
    {
        $sql = "INSERT INTO steps (ill_id,step_num,step_text)
        		VALUES(?,?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum,$text]);
    }

    /**
     * Adds picture path to some illness step
     * @param  int    $illnessId
     * @param  int    $stepNum
     * @param  string $path
     * @return void
     */
    public function addPictureToStep(
        int $illnessId, int $stepNum, string $path
    ) : void
    {
        $sql = "INSERT INTO illpic (ill_id,step_num,pic_path)
    	        VALUES(?,?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum,$path]);
    }

    /**
     * Adds video path to some illness step
     * @param  int    $illnessId
     * @param  int    $stepNum
     * @param  string $path
     * @return void
     */
    public function addVideoToStep(
        int $illnessId, int $stepNum, string $path
    ) : void
    {
        $sql = "INSERT INTO illvid (ill_id,step_num,vid_path)
    	        VALUES(?,?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum,$path]);
    }

    /**
     * Links drug to an illness
     * @param  int    $illnessId
     * @param  string $drugId
     * @return void
     */
    public function addDrugToIllness(int $illnessId, string $drugId) : void
    {
        $sql = "INSERT INTO illdrug(ill_id,drug_id)
        		VALUES(?,?) ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$drugId]);
    }

    /**
     * Links payment to an illness
     * @param  int    $illnessId
     * @param  string $paymentId
     * @return void
     */
    /*public function addPaymentToIllness(int $illnessId, int $paymentId) : void
    {
        $sql = "INSERT INTO payments(ill_id,pay_id,pay_name,pay_cost)
        		VALUES('$illnessId','$paymentId','$paymentname','$paymentcost') ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([]);
    }*/

    public function createUser_stub($username, $password) : User
    {
        return new User(
            1,
            $username,
            $password,
            1,
            date("Y-m-d H:i:s")
        );
    }
}
