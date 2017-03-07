<?php
namespace VVC\Model\Database;

use VVC\Model\Data\User;
use VVC\Model\Data\IllnessCollection;
use VVC\Model\Data\IllnessRecord;

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

        return new User(
            $result['id'],
            $result['username'],
            $result['password'],
            $result['role_id'],
            $result['created_at']
        );
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

        return new User(
            $result['id'],
            $result['username'],
            $result['password'],
            $result['role_id'],
            $result['created_at']
        );
    }

    /**
     * Returns all illnesses collection
     * Only basic info is needed, no steps array
     * @return IllnessCollection, empty or not
     */
    public function getAllIllnesses() : IllnessCollection
    {
        if (NO_DATABASE) {
            $collection = new IllnessCollection();
            $collection->add(new IllnessRecord(
                1,
                'Illness 1',
                'Class 1',
                'Illness 1 description.',
                2
            ));
            $collection->add(new IllnessRecord(
                2,
                'Illness 2',
                'Class 1',
                'Illness 2 description.',
                0
            ));
            $collection->add(new IllnessRecord(
                3,
                'Illness 3',
                'Class 2',
                'Illness 3 description.',
                1
            ));

            return $collection;
        }

        $sql = "SELECT * FROM illnesses";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $collection = new IllnessCollection();

        while ($row = $result->fetch(\PDO::FETCH_ASSOC)){
            $collection->add(new IllnessRecord(
                $row['id'],
                $row['name'],
                $row['class'],
                $row['description'],
                $row['stay']
            ));
        }

        return $collection;
    }

    // getIllnessById - return illness
    // getAllIllnesses (limit = null, offset = null)

    // getClassByName
    // getAllClasses

    // getDrugById ?
    // getAllDrugs

    // getPaymentById ?
    // getAllPayments

    public function findUserByUsername_stub($username)
    {
        if ($username == ADMIN_NAME){
            return new User(
                1,
                ADMIN_NAME,
                password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT),
                1,
                date("Y-m-d H:i:s")
            );
        } elseif ($username == USER_NAME) {
            return new User(
                2,
                USER_NAME,
                password_hash(USER_PASSWORD, PASSWORD_DEFAULT),
                2,
                date("Y-m-d H:i:s")
            );
        } else {
            return false;
        }
    }
}
