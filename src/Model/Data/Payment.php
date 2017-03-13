<?php
namespace VVC\Model\Data;

class Payment
{
    private $id = 0;
    private $name = 'Payment';
    //private $amount = null;
    private $cost=null;
    private $number =0;

    public function __construct(int $id, string $name,/* float $amount*/float $cost,int $number)
    {
        $this->setId($id);
        $this->setName($name);
        //$this->setAmount($amount);
        $this->setCost($cost);
        $this->setNumber($number);
    }

    public function getId() : int
    {
        return $this->ill_id;
        //return $this->id;
    }

    public function setId(int $id)
    {
        $this->ill_id=$id;
        //$this->id = $id;
    }

    public function getName() : string
    {
        return $this->pay_name;
        //return $this->name;
    }

    public function setName(string $name)
    {
    	$this->pay_name = $name;
        //$this->name = $name;
    }

   /* public function getAmount() : float
    {
        return $this->amount;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }*/
    
    public function getCost():float
    {
    	return $this->cost;
    }
    
    public function setCost(float $cost)
    {
    	$this->cost=$cost;
    }
    
    public function getNumber():int
    {
    	return $this->number;
    }
    
    public function setNumber(float $number)
    {
    	$this->number=$number;
    }
    
}
