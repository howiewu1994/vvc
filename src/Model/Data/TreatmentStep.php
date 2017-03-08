<?php
namespace VVC\Model\Data;

class TreatmentStep
{
    private $seqNum = 1;
    private $name = 'Step One';
    private $text = 'First, doctors need to...';

    private $pictures = [];
    private $videos = [];

    private $drugs = [];
    private $payments = [];

    public function __construct(int $seqNum, string $name, string $text)
    {
        $this->setSeqNum($seqNum);
        $this->setName($name);
        $this->setText($text);
    }

    public function getSeqNum() : int
    {
        return $this->seqNum;
    }

    public function setSeqNum(int $seqNum)
    {
        $this->seqNum = $seqNum;
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

    public function getPictures() : array
    {
        return $this->pictures;
    }

    public function setPictures(array $pictures)
    {
        $this->pictures = $pictures;
    }

    public function addPictures(array $pictures)
    {
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }
    }

    public function addPicture(string $pathToPicture)
    {
        $this->pictures[] = $pathToPicture;
    }

    public function getVideos() : array
    {
        return $this->videos;
    }

    public function setVideos(array $videos)
    {
        $this->videos = $videos;
    }

    public function addVideos(array $videos)
    {
        foreach ($videos as $video) {
            $this->addVideo($video);
        }
    }

    public function addVideo(string $pathToVideo)
    {
        $this->videos[] = $pathToVideo;
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

}
