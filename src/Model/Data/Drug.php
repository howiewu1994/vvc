<?php
namespace VVC\Model\Data;

class Drug
{
    private $id = 'AB-123';
    private $name = 'Basic drug';
    private $text = 'Best drug for...';
    private $picture = null;
    private $cost = null;

    public function __construct(
        string  $id,
        string  $name,
        string  $text,
        string  $picture,
        float   $cost
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setText($text);
        $this->setPicture($picture);
        $this->setCost($cost);
    }

    public function getId() : string
    {
    	// return $this->drug_id;
    	return $this->id;
    }

    public function setId(string $id)
    {
        // $this->drug_id=$id;
    	$this->id = $id;
    }

    public function getName() : string
    {
    	// return $this->drug_name;
    	return $this->name;
    }

    public function setName(string $name)
    {
    	// $this->drug_name = $name;
    	$this->name = $name;
    }

    public function getText() : string
    {
    	// return $this->drug_text;
    	return $this->text;
    }

    public function setText(string $text)
    {
    	// $this->drug_text = $text;
        $this->text = $text;
    }

    public function getPicture() : string
    {
    	// return $this->drug_picture;
        return $this->picture;
    }

    public function setPicture(string $picture)
    {
    	// $this->drug_picture = $picture;
        $this->picture = $picture;
    }

    public function getCost() : float
    {
        // return $this->drug_cost;
    	return $this->cost;
    }

    public function setCost(float $cost)
    {
        // $this->drug_cost=$cost;
    	$this->cost = $cost;
    }
}
