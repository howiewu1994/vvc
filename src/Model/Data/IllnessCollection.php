<?php
namespace VVC\Model\Data;

/**
 * [                IllnessCollection                    ]
 * [class => [              IllnessRecord                ]
 *           [id => [id, name, class, description, stay]]]
 *
 *
 * Example :    Class 1 -- id 1 -- name 1
 *                       |       - description 1
 *                       |       - 2 days
 *                       - id 2 -- name 2
 *                               - description 2
 *                               - 0 days
 *              Class 2 -- id 3 -- name 3
 *                               - description 3
 *                               - 1 day
 */
class IllnessCollection
{
    private $records = [];

    public function __construct($records = null)
    {
        if ($records) {
            $this->setRecords($records);
        }
    }

    public function getRecords() : array
    {
        return $this->records;
    }

    public function setRecords(array $records)
    {
        $this->records = $records;
    }

    public function add(IllnessRecord $record)
    {
        $class = $record->getClass();
        $id = $record->getId();

        $this->records[$class][$id] = $record;
    }

    public function getClasses() : array
    {
        return array_keys($this->records);
    }

}
