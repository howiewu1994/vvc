<?php
namespace VVC\Model\Database;

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
        if ($username == 'admin'){
            return [
                'id' => 1,
                'username' => 'admin',
                'password' => password_hash('123', PASSWORD_DEFAULT),
                'role_id' => 1,
                'created_at' => date("Y-m-d H:i:s")
            ];
        } elseif ($username == 'user') {
            return [
                'id' => 2,
                'username' => 'user',
                'password' => password_hash('123', PASSWORD_DEFAULT),
                'role_id' => 2,
                'created_at' => date("Y-m-d H:i:s")
            ];
        } else {
            return [];
        }
    }
}
