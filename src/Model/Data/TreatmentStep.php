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

    public function getVideos() : array
    {
        return $this->videos;
    }

    public function setVideos(array $videos)
    {
        $this->videos = $videos;
    }

    public function getDrugs() : array
    {
        return $this->drugs;
    }

    public function setDrugs(array $drugs)
    {
        $this->drugs = $drugs;
    }

    public function getPayments() : array
    {
        return $this->payments;
    }

    public function setPayments(array $payments)
    {
        $this->payments = $payments;
    }

}
