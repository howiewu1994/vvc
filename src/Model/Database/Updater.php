<?php
namespace VVC\Model\Database;

/**
 * Processes SELECT queries
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
        // test stub
        if (NO_DATABASE) {
            return;
        }

        $sql = "UPDATE users SET password = ? where id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$password, $userId]);
    }

    /**
     * Rewrites user details based on id
     * @param  int    $id
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
    ) : void {

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
    ) : void {

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

    }

    /**
     * Rewrites number of days for hospitalization based on illness id
     * @param  int  $illnessId  - not updated
     * @param  int  $days
     * @return void
     */
    public function updateStay(int $illnessId, int $days) : void
    {

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
    ) : void {

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

    }

    /**
     * Rewrites step text based on illness id and step id (num)
     * @param  int    $illnessId - not updated
     * @param  int    $num       - not updated
     * @param  string $text
     * @return void
     */
    public function updateStepText(int $illnessId, int $num, string $text) : void
    {

    }

    /**
     * Replaces all current step pictures with new pics array
     * @param  int    $stepNum - not updated
     * @param  array  $pics    - may consist of only one picture
     * @return void
     */
    public function updateStepPictures(int $stepNum, array $pics) : void
    {
        (new Deleter())->removeAllPicturesFromStep($stepNum);

        foreach ($pics as $pic) {
            (new Creator())->addPictureToStep($stepNum, $pic);
        }
    }

    /**
     * Replaces all current step videos with new vids array
     * @param  int    $stepNum - not updated
     * @param  array  $vids    - may consist of only one video
     * @return void
     */
    public function updateStepPictures(int $stepNum, array $vids) : void
    {
        (new Deleter())->removeAllVideosFromStep($stepNum);

        foreach ($vids as $vid) {
            (new Creator())->addVideoToStep($stepNum, $vid);
        }
    }

}
