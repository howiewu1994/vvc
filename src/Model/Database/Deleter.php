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
     * @return void
     */
    public function deleteUser(int $userId) : void
    {
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }

    /**
     * Deletes illness and all associated details based on id
     * Does NOT delete actual drugs, payments, etc.
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  int   $illnessId
     * @return true if successful OR false if rolled back
     */
    public function deleteIllness(int $illnessId) : bool
    {
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
            $sql = "DELETE FROM ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$illnessId]);

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
     * This is a part of deleteIllness transaction
     *
     * Removes all details associated with illness step
     * @param  int    $illnessId
     * @param  int    $stepNum
     * @return void
     */
    public function removeStep(int $illnessId, int $stepNum) : void
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
    public function removeTextFromStep(int $illnessId, int $stepNum) : void
    {
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all pictures linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllPicturesFromStep(int $illnessId, int $stepNum) : void
    {
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
    }

    /**
     * This is a part of deleteIllness transaction
     *
     * Removes all videos linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllVideosFromStep(int $illnessId, int $stepNum) : void
    {
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([/* */]);
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
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
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
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
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
        $sql = "DELETE FROM ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$illnessId]);
    }

    /**
     * Deletes drug and removes it from all illnesses
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  string $drugId
     * @return true if successful OR false if rolled back
     */
    public function deleteDrug(string $drugId) : bool
    {
        // Turn autocommit off
        $this->db->beginTransaction();

        try {
            // Remove drug from all illnesses
            $illnesses = (new Reader())->findIllnessByDrugId($drugId);
            foreach ($illnesses as $illness) {
                $illnessId = $illness->getId();
                $this->removeDrugFromIllness($illnessId, $drugId);
            }

            // Delete drug
            $sql = "DELETE FROM ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$drugId]);

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
     * Deletes payment and removes it from all illnesses
     *
     * If any of deletions fail, roll back transaction
     *
     * @param  int $paymentId
     * @return true if successful OR false if rolled back
     */
    public function deletePayment(int $paymentId) : bool
    {
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
            $sql = "DELETE FROM ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$paymentId]);

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
}
