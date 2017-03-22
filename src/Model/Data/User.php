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
        $id = -1,
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
        // return $this->user_id;
        return $this->id;
    }

    public function setId(int $id)
    {
    	// $this->user_id = $id;
        $this->id = $id;
    }

    public function getUsername() : string
    {
    	// return $this->user_name;
    	return $this->username;
    }

    public function setUsername(string $username)
    {
    	// $this->use_rname = $username;
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
    	// return $this->role_id;
    	return $this->roleId;
    }

    public function setRoleId(int $roleId)
    {
    	// $this->role_id = $roleId;
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
