<?php
namespace Jaspersoft\Dto\Job;

class SimpleTrigger extends Trigger
{

    /**
     * How many times trigger will fire
     * @var int
     */
    public $occurrenceCount;

    /**
     * Time interval trigger should fire, unit provided separately.
     * @var int
     */
    public $recurrenceInterval;

    /**
     * Unit that $recurrenceInterval represents.
     *
     * Supported Values:
     *   "MINUTE", "HOUR", "DAY", "WEEK"
     *
     * @var string
     */
    public $recurrenceIntervalUnit;

    public function __construct($occurrenceCount = null, $recurrenceInterval = null, $recurrenceIntervalUnit = null)
    {
        $this->occurrenceCount = $occurrenceCount;
        $this->recurrenceInterval = $recurrenceInterval;
        $this->recurrenceIntervalUnit = $recurrenceIntervalUnit;
    }

    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v) {
            $result->$k = $v;
        }
        return $result;
    }

} 