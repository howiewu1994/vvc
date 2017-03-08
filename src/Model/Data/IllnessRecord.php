<?php
namespace VVC\Model\Data;

class IllnessRecord
{
    private $id = 0;
    private $name = 'Illness';
    private $class = 'General';
    private $description = 'Very basic pet illness.';
    // how many days - 0 OR 1,2,3..
    private $stay = 0;

    private $steps = [];

    public function __construct(
        int     $id,
        string  $name,
        string  $class,
        string  $description,
        int     $stay
    ) {
        $this->setId($id);
        $this->setName($name);
        $this->setClass($class);
        $this->setDescription($description);
        $this->setStay($stay);
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id)
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

    public function getClass() : string
    {
        return $this->class;
    }

    public function setClass(string $class)
    {
        $this->class = $class;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getStay() : int
    {
        return $this->stay;
    }

    public function setStay(int $stay)
    {
        $this->stay = $stay;
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

    public function addStep(TreatmentStep $step)
    {
        $seqNum = $step->getSeqNum();
        $this->steps[$seqNum] = $step;
        ksort($this->steps);
    }

}
