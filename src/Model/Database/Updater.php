<?php
namespace VVC\Model\Database;

/**
 * Processes SELECT queries
 */
class Updater extends Connection
{
    /**
     * Changes user password
     * @param  int $userId      
     * @param  string $password - already hashed
     * @return true
     */
    public function changePassword($userId, $password)
    {
        // test stub
        if (NO_DATABASE) {
            return false;
        }

        $sql = "UPDATE users SET password = ? where id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$password, $userId]);
        return true;
    }

}
