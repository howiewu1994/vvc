<?php
namespace VVC\Model\Data;

class Payment
{
    private $id = 0;
    private $illnessId = 0;
    private $name = 'Payment';
    private $cost = 0.0;
    private $number = 0;

    public function __construct(
        $id = -1,
        int $illnessId,
        string $name,
        float $cost,
        int $number
    ) {
        $this->setId($id);
        $this->setIllnessId($illnessId);
        $this->setName($name);
        $this->setCost($cost);
        $this->setNumber($number);
    }

    public function getId() : int
    {
        // return $this->ill_id;
        return $this->id;
    }

    public function setId(int $id)
    {
        // $this->ill_id=$id;
        $this->id = $id;
    }

    public function setIllnessId(int $illnessId)
    {
        $this->illnessId = $illnessId;
    }

    public function getIllnessId() : int
    {
        return $this->$illnessId;
    }

    public function getName() : string
    {
        // return $this->pay_name;
        return $this->name;
    }

    public function setName(string $name)
    {
    	// $this->pay_name = $name;
        $this->name = $name;
    }

    public function getCost() : float
    {
    	return $this->cost;
    }

    public function setCost(float $cost)
    {
    	$this->cost = $cost;
    }

    public function getNumber() : int
    {
    	return $this->number;
    }

    public function setNumber(int $number)
    {
    	$this->number = $number;
    }

    public function getTotal(string $currency = null) : float
    {
        $total = $this->getCost() * $this->getNumber();
        return $total . $currency;
    }
}
