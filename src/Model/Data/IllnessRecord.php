<?php
namespace VVC\Model\Data;

class IllnessRecord
{
    private $id = 0;
    private $name = 'Illness';
    private $class = 'General';
    private $description = 'Very basic pet illness.';

    private $steps = [];

    private $drugs = [];
    private $payments = [];
    private $stay = null;

    public function __construct(
        int     $id,
        string  $name,
        string  $class,
        $description = ""
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setClass($class);
        $this->setDescription($description);
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

    public function getName() : string
    {
        // return $this->ill_name;
        return $this->name;
    }

    public function setName(string $name)
    {
        // $this->ill_name=$name;
        $this->name = $name;
    }

    public function getClass() : string
    {
        // return $this->class_name;
        return $this->class;
    }

    public function setClass(string $class)
    {
        // $this->class_name=$class;
        $this->class = $class;
    }

    public function getDescription() : string
    {
        // return $this->ill_describe;
        return $this->description;
    }

    public function setDescription(string $description)
    {
        // $this->ill_describe=$description;
        $this->description = $description;
    }

    public function getSteps() : array
    {
        return $this->steps;
    }

    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    public function addSteps(array $steps)
    {
        foreach ($steps as $step) {
            $this->addStep($step);
        }
    }

    public function addStep(Step $step)
    {
        $seqNum = $step->getNum();
        $this->steps[$seqNum] = $step;
        ksort($this->steps);
    }

    public function getDrugs() : array
    {
        return $this->drugs;
    }

    public function setDrugs(array $drugs)
    {
        $this->drugs = $drugs;
    }

    public function addDrugs(array $drugs)
    {
        foreach ($drugs as $drug) {
            $this->addDrug($drug);
        }
    }

    public function addDrug(Drug $drug)
    {
        $drugId = $drug->getId();
        $this->drugs[$drugId] = $drug;
        ksort($this->drugs);
    }

    public function getPayments() : array
    {
        return $this->payments;
    }

    public function setPayments(array $payments)
    {
        $this->payments = $payments;
    }

    public function addPayments(array $payments)
    {
        foreach ($payments as $payment) {
            $this->addPayment($payment);
        }
    }

    public function addPayment(Payment $payment)
    {
        $paymentId = $payment->getId();
        $this->payments[$paymentId] = $payment;
        ksort($this->payments);
    }

    public function getStay() : Stay
    {
        return $this->stay;
    }

    public function setStay(Stay $stay)
    {
        $this->stay = $stay;
    }

}
