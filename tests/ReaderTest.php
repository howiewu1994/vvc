<?php
namespace VVC\Test;

require_once __DIR__ . '/../web/config.php';

// use VVC\Model\Database\Creator;
// use VVC\Model\Database\Deleter;
use VVC\Model\Database\Reader;
// use VVC\Model\Database\Updater;

use VVC\Model\Data\User;

class ReaderTest extends DBTest
{
    /**
     * @test
     */
    public function findUserByUsername_Test()
    {
        // Expect User
        foreach ($this->data['users'] as $user) {
            $input[] = $user['user_name'];
            $expected[] = new User(
                $user['user_id'],
                $user['user_name'],
                $user['password'],
                $user['role_id'],
                $user['createdAt']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $user = $dbReader->findUserByUsername($input[$i]);
            $this->assertEquals($expected[$i], $user);
        }
    }

    /**
     * @test
     */
    public function findUserById_Test()
    {
        // Expect User
        foreach ($this->data['users'] as $user) {
            $input[] = $user['user_id'];
            $expected[] = new User(
                $user['user_id'],
                $user['user_name'],
                $user['password'],
                $user['role_id'],
                $user['createdAt']
            );
        }
        // Expect false
        $input[] = -1;
        $expected[] = false;

        $dbReader = new Reader($this->db);

        for ($i = 0; $i < count($input); $i++) {
            $user = $dbReader->findUserById($input[$i]);
            $this->assertEquals($expected[$i], $user);
        }
    }


}
