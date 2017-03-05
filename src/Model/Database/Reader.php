<?php
namespace VVC\Model\Database;

class Reader
{
    public static function findUserByUsername($username)
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
            return false;
        }
    }
}
