<?php

namespace Jaspersoft\Dto\Job;


/**
 * Class Trigger
 *
 * This class contains attributes shared among both SimpleTrigger and CalendarTrigger
 *
 * @package Jaspersoft\Dto\Job
 */
abstract class Trigger {

    public $timezone;
    public $calendarName;
    public $startType;
    public $startDate;
    public $endDate;
    public $misfireInstruction;

    public function __construct() {

    }

    public function name() {
        $type = explode('\\', get_class($this));
        $type = end($type);

        return $type;
    }

    public function toJSON()
    {
        return json_encode(array("trigger" => array($this->name() => $this->jsonSerialize())));
    }

    public function jsonSerialize()
    {
        $result = array();
        foreach (get_object_vars($this) as $k => $v)
            if (isset($v))
                $result[$k] = $v;
        return $result;
    }


} 