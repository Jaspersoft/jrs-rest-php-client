<?php
namespace Jaspersoft\Dto\Job;
use Jaspersoft\Dto\DTOObject;

/**
 * Class Trigger
 *
 * Contains attributes shared among both SimpleTrigger and CalendarTrigger
 *
 * @package Jaspersoft\Dto\Job
 */
abstract class Trigger extends DTOObject
{

    /**
     * Read-only value of server-declared ID
     *
     * @var int
     */
    public $id;

    /**
     * Read-only value used for optimistic locking
     *
     * @var int
     */
    public $version;

    /**
     * Timezone of the job to trigger
     *
     * Example: "America/Los_Angeles"
     *
     * @var string
     */
    public $timezone;

    /**
     * Name of the calendar to follow
     *
     * Example: "someExistingCalendarName"
     *
     * @var string
     */
    public $calendarName;

    /**
     * Start type for trigger
     *
     * Supported Values:
     *   1 - Job should be scheduled immediately
     *   2 - Job should be scheduled at start date
     *
     * @var int
     */
    public $startType;

    /**
     * Date which job should start. Timezone described by $timezone is used.
     *
     * Date Format: "yyyy-MM-dd HH:mm"
     *
     * @var string
     */
    public $startDate;

    /**
     * Date when job trigger should stop executing. Timezone described by $timezone is used.
     *
     * Date Format: "yyyy-MM-dd HH:mm"
     *
     * @var string
     */
    public $endDate;

    /**
     * A misfire occurs if persistent trigger "misses" its time due to scheduler being shutdown, or lack of threads
     * in thread pool to execute job with.
     *
     * Supported Values:
     *
     * __All Trigger Types__
     *
     *   -1 - Ignore misfire policy, fire trigger as soon as it can, update Trigger as if it fired at proper time
     *   0 - No instruction (default) (same behaviour as -1)
     *   1 - Instruct scheduler on misfire to now fire trigger
     *
     * __SimpleTrigger Types__
     *
     *   2 - SimpleTrigger is rescheduled for 'now' and repeat count is left as-is. End time is still honored.
     *   3 - SimpleTrigger is rescheduled for 'now' and repeat count is set to what it would be if no misfires occurred.
     *       End time is still honored.
     *   4 - SimpleTrigger is schduled for time after 'now' taking into account any associated Calendar, repeat count
     *       set to what it woudl be had no misfires occurred.
     *   5 - SimpleTrigger scheduled after 'now' taking into account nay associated Calendar,
     *       repeat count left unchanged.
     *
     * __CalendarTrigger Types__
     *
     *   2 - CalendarTrigger wants to have next-fire-time updated to next time on schedule, but not fired now.
     *
     * @var int
     */
    public $misfireInstruction;

    public function name()
    {
        $type = explode('\\', get_class($this));
        $type = lcfirst(end($type));
        return $type;
    }

    public function jsonSerialize()
    {
        return array($this->name() => parent::jsonSerialize());
    }

    public static function createFromJSON($json_obj)
    {
        if (isset($json_obj->simpleTrigger)) {
            return SimpleTrigger::createFromJSON($json_obj->simpleTrigger);
        }
        else if (isset($json_obj->calendarTrigger)) {
            return CalendarTrigger::createFromJSON($json_obj->calendarTrigger);
        }
        else {
            //TODO: add proper exception handling
            return null;
        }
    }

} 