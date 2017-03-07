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
        $username,
        $password,
        $role_id = 2,
        $created_at = null)
    {
        // test stub
        if (NO_DATABASE) {
            return $this->createUser_stub($username, $password);
        }

        if ($created_at == null) {
            $created_at = date("Y-m-d H:i:s");
        }

        $sql = "INSERT INTO
            users(username, password, role_id, created_at)
            VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $password, $role_id, $created_at]);
        return (new Reader())->findUserByUsername($username);
    }

    public function createUser_stub($username, $password) : array
    {
        return new User([
            'id' => 1,
            'username' => $username,
            'password' => $password,
            'role_id' => 1,
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
