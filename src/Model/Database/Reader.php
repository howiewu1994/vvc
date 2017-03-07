<?php
namespace VVC\Model\Database;

use VVC\Model\Data\User;

/**
 * Processes SELECT queries
 */
class Reader extends Connection
{
    /**
     * Returns existing user data
     * @param  string $username
     * @return User OR false
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
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return false;
        }

        return new User($result);
    }

    /**
     * Returns existing user data
     * @param  int $userId
     * @return User OR false
     */
    public function findUserById($userId)
    {
        // test stub
        if (NO_DATABASE) {
            return false;
        }

        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) {
            return false;
        }

        return new User($result);
    }

    // getIllnessById - return illness
    // getIllnesses (limit = null, offset = null)

    // getClassByName
    // getAllClasses

    // getDrugById ?
    // getAllDrugs

    // getPaymentById ?
    // getAllPayments

    public function findUserByUsername_stub($username)
    {
        if ($username == ADMIN_NAME){
            return new User([
                'id' => 1,
                'username' => ADMIN_NAME,
                'password' => password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT),
                'role_id' => 1,
                'created_at' => date("Y-m-d H:i:s")
            ]);
        } elseif ($username == USER_NAME) {
            return new User([
                'id' => 2,
                'username' => USER_NAME,
                'password' => password_hash(USER_PASSWORD, PASSWORD_DEFAULT),
                'role_id' => 2,
                'created_at' => date("Y-m-d H:i:s")
            ]);
        } else {
            return false;
        }
    }
}
