<?php
namespace VVC\Model\Data;

/**
 * [                IllnessCollection                    ]
 * [class => [              IllnessRecord                ]
 *           [id => [id, name, class, description, stay]]]
 *
 *
 * Example :    Class 1 -- id 1 -- id 1
 *                       |       - name 1
 *                       |       - class 1
 *                       |       - description 1
 *                       |       - 2 days
 *                       - id 2 -- id 2
 *                               - name 2
 *                               - class 1
 *                               - description 2
 *                               - 0 days
 *              Class 2 -- id 3 -- id 3
 *                               - name 3
 *                               - class 2
 *                               - description 3
 *                               - 1 day
 */
class IllnessCollection
{
    private $records = [];

    public function __construct($records = null)
    {
        if ($records) {
            $this->addRecords($records);
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

    public function addRecords(array $records)
    {
        foreach ($records as $record) {
            $this->addRecord($record);
        }
    }

    public function addRecord(IllnessRecord $record)
    {
        $class = $record->getClass();
        $id = $record->getId();

        $this->records[$class][$id] = $record;
        // TODO sorting.
        //ksort($this->records[$class]);
    }

    public function getClasses() : array
    {
        return array_keys($this->records);
    }

}
