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
        string  $password,
                $roleId = 2,
        string  $createdAt = null
    ) {
        // test stub
        if (NO_DATABASE) {
            return $this->createUser_stub($username, $password);
        }

        if ($createdAt == null) {
            $createdAt = date("Y-m-d H:i:s");
        }

        $sql = "INSERT INTO
            users(username, password, role_id, created_at)
            VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $password, $roleId, $createdAt]);
        return (new Reader())->findUserByUsername($username);
    }


    public function createUser_stub($username, $password) : array
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
