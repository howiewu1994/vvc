<?php
namespace VVC\Model\Database;

/**
 * Processes DELETE queries
 */
class Deleter extends Connection
{
    /**
     * Removes all pictures linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllPicturesFromStep(int $illnessId, int $stepNum) : void
    {

    }

    /**
     * Removes all videos linked to a step
     * @param  int  $illnessId
     * @param  int  $stepNum
     * @return void
     */
    public function removeAllVideosFromStep(int $illnessId, int $stepNum) : void
    {

    }
}
