<?php
namespace VVC\Model\Data;

class Stay
{
    private $days = 0;

    public function __construct(int $days)
    {
        $this->setDays($days);
    }

    public function getDays() : int
    {
        return $this->days;
    }

    public function setDays(int $days)
    {
        $this->days = $days;
    }
}
