<?php
namespace VVC\Model\Data;

class User
{
    private $id;
    private $username;
    private $password;
    private $roleId;
    private $createdAt;

    public function __construct(
        int     $id,
        string  $username,
        string  $password,
        int     $roleId,
        string  $createdAt
    ) {
        $this->setId($id);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setRoleId($roleId);
        $this->setCreatedAt($createdAt);
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getRoleId() : int
    {
        return $this->roleId;
    }

    public function setRoleId(int $roleId)
    {
        $this->roleId = $roleId;
    }

    public function getCreatedAt() : string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
