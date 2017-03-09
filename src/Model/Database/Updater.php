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
     * @return void
     */
    public function changePassword($userId, $password) : void
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
     * Rewrite user details based on id
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

}
