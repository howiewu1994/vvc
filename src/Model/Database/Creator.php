<?php
namespace VVC\Model\Database;

class Creator
{
    public static function createUser($username, $password)
    {
        return [
            'id' => 3,
            'username' => $username,
            'password' => $password,
            'role_id' => 1
        ];
    }
}
