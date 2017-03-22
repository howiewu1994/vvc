<?php
namespace VVC\Model\Database;

/**
 * Processes DELETE queries
 */
class Deleter extends Connection
{
    /**
     * Deletes user based on id
     * @param  int   $userId
     * @return deleted user OR false
     */
    public function deleteUser(int $userId)
    {
        $oldUser = (new Reader())->findUserById($userId);

        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        if ($stmt->rowCount() == 0) {
            return false;
        }

        return $oldUser;
    }

    /**
     * Deletes illness and all associated details based on id
     * Does NOT delete actual drugs, payments, etc.
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  int   $illnessId
     * @return deleted illness if successful OR false if rolled back
     */
    public function deleteIllness(int $illnessId)
    {
        $oldIll = (new Reader())->findIllnessById($illnessId);

        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Remove all steps
            $steps = (new Reader())->findIllnessSteps($illnessId);
            foreach ($steps as $step) {
                $stepNum = $step->getNum();
                $this->removeStep($illnessId, $stepNum);
            }

            // Remove all additional details
            $this->removeAllDrugsFromIllness($illnessId);
            $this->removeAllPaymentsFromIllness($illnessId);
            $this->removeStayFromIllness($illnessId);

            // In the end delete illness
            $sql = "DELETE FROM illness
            		WHERE ill_id=? ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$illnessId]);

            // Commit transaction
            $this->db->commit();
            return $oldIll;

        } catch (\Exception $e) {
            Logger::log(
                'db', 'error',
                "Failed to delete illness $illnessId, rolled back transaction",
                $e
            );
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all details associated with illness step
     * @param  int    $illnessId
     * @param  int    $stepNum
     * @return void
     */
    public function removeStep(int $illnessId, int $stepNum)
    {
        $this->removeTextFromStep($illnessId, $stepNum);
        $this->removeAllPicturesFromStep($illnessId, $stepNum);
        $this->removeAllVideosFromStep($illnessId, $stepNum);
        // Step is now removed, return
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes text linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeTextFromStep(int $illnessId, int $stepNum)
    {
        $sql = "DELETE FROM steps
        		WHERE ill_id=? AND step_num=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all pictures linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllPicturesFromStep(int $illnessId, int $stepNum)
    {
        $sql = "DELETE FROM illpic
        		WHERE ill_id=? AND step_num=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum]);
    }

    public function removePictureFromStep(
        int $illnessId, int $stepNum, string $path
    ) {
        $sql = "DELETE FROM illpic
        		WHERE ill_id=? AND step_num=? AND pic_path=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum,$path]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all videos linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllVideosFromStep(int $illnessId, int $stepNum)
    {
        $sql = "DELETE FROM illvid
        		WHERE ill_id=? AND step_num=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum]);
    }

    public function removeVideoFromStep(
        int $illnessId, int $stepNum, string $path
    ) {
        $sql = "DELETE FROM illvid
        		WHERE ill_id=? AND step_num=? AND vid_path=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId,$stepNum,$path]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all drugs linked to an illness
     * @param  int    $illnessId
     * @return void
     */
    public function removeAllDrugsFromIllness(int $illnessId)
    {
        $sql = "DELETE FROM illdrug
        		WHERE ill_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
    }

    public function removeDrugFromIllness(int $illnessId, string $drugId)
    {
        $sql = "DELETE FROM illdrug
        		WHERE ill_id=? AND drug_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $drugId]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all payments linked to an illness
     * @param  int    $illnessId
     * @return void
     */
    public function removeAllPaymentsFromIllness(int $illnessId)
    {
        $sql = "DELETE FROM payments
        		WHERE ill_id=? ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
    }

    public function removePaymentFromIllness(int $illnessId, int $paymentId)
    {
        $sql = "DELETE FROM payments
        		WHERE ill_id=? AND pay_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId, $paymentId]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes stay details linked to an illness
     * @param  int    $illnessId
     * @return void
     */
    public function removeStayFromIllness(int $illnessId)
    {
        $sql = "DELETE FROM payments
        		WHERE ill_id=? AND pay_name='stay'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
    }

    /**
     * Deletes picture from all steps AND from all drugs
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  string $path
     * @return true if successful OR false if rolled back
     */
    public function deletePicture(string $path)
    {
        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Delete from illnesses
            $sql = "DELETE FROM illpic
            		WHERE pic_path=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$path]);

            // Delete from drugs
            $sql = "UPDATE drug
            		SET drug_picture=''
                    WHERE drug_picture=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$path]);

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            Logger::log(
                'db', 'error',
                "Failed to delete picture $path, rolled back transaction",
                $e
            );
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Deletes video from all steps
     * @param  string $path
     * @return int  - how many rows were deleted
     */
    public function deleteVideo(string $path)
    {
        $sql = "DELETE FROM illvid
        		WHERE vid_path=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$path]);
        return $stmt->rowCount();
    }

    /**
     * Deletes drug and removes it from all illnesses
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  string $drugId
     * @return deleted drug if successful OR false if rolled back
     */
    public function deleteDrug(string $drugId)
    {
        $oldDrug = (new Reader())->findDrugById($drugId);

        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Remove drug from all illnesses
            $illnesses = (new Reader())->findIllnessesByDrugId($drugId);
            foreach ($illnesses as $illness) {
                $illnessId = $illness->getId();
                $this->removeDrugFromIllness($illnessId, $drugId);
            }

            // Delete drug
            $sql = "DELETE FROM drug
            		WHERE drug_id=? ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$drugId]);

            // Commit transaction
            $this->db->commit();
            return $oldDrug;

        } catch (\Exception $e) {
            Logger::log(
                'db', 'error',
                "Failed to delete drug $drugId, rolled back transaction",
                $e
            );
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Deletes payment and removes it from all illnesses
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  int $paymentId
     * @return deleted payment if successful OR false if rolled back
     */
    public function deletePayment(int $paymentId)
    {
        $oldPayment = (new Reader())->findPaymentById($paymentId);

        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Remove payment from all illnesses
            $illnesses = (new Reader())->findIllnessByPaymentId($paymentId);
            foreach ($illnesses as $illness) {
                $illnessId = $illness->getId();
                $this->removePaymentFromIllness($illnessId, $paymentId);
            }

            // Delete payment
            $sql = "DELETE FROM payment
            		WHERE pay_id=? ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$paymentId]);

            // Commit transaction
            $this->db->commit();
            return $oldPayment;

        } catch (\Exception $e) {
            Logger::log(
                'db', 'error',
                "Failed to delete payment $paymentId, rolled back transaction",
                $e
            );
            $this->db->rollBack();
            return false;
        }
    }
}
