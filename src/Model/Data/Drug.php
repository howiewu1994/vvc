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
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function getPicture() : string
    {
        return $this->picture;
    }

    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    public function getCost() : float
    {
        return $this->cost;
    }

    public function setCost(float $cost)
    {
        $this->cost = $cost;
    }


}
