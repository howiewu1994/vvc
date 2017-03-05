<?php
namespace VVC\Model\Database;

use VVC\Model\Database\Connection;

/**
 * Processes SELECT queries
 */
class Reader extends Connection
{
    /**
     * Returns existing user data
     * @param  string $username
     * @return [id, username, password, role_id, created_at] OR false
     */
    public function findUserByUsername($username)
    {
        // test stub
        if (NO_DATABASE) {
            return $this->findUserByUsername_stub($username);
        }

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findUserByUsername_stub($username)
    {
        if ($username == 'user'){
            return [
                'id' => 1,
                'username' => 'user',
                'password' => '123',
                'role_id' => 2
            ];
        } elseif ($username == 'admin') {
            return [
                'id' => 2,
                'username' => 'admin',
                'password' => '123',
                'role_id' => 1
            ];
        } else {
            return [];
        }
    }
}
