<?php
namespace VVC\Model\Data;

class TherapyStep extends Step
{
    protected $num = 4;
    protected $name = 'Therapy';
    protected $text = 'Therapy includes...';

    private $drugs = [];
    private $payments = [];
    private $stay = null;

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
