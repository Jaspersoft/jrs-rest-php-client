<?php
namespace Jaspersoft\Dto\Job;

class CalendarTrigger extends Trigger
{

    /**
     * Pattern that determines minute part of trigger fire times
     *
     * Pattern can consist of following tokens:
     *   + single minute value between 0 and 59 these can be separated by commas to create a set
     *        (e.g: "5" OR "5,7,9,11,13")
     *   + a range of minutes (e.g: "0-10" fires every minute from HH:00 to HH:10)
     *       ranges can also be separated by commas (e.g: "0-10,30-40")
     *   + a minute value with an increment (e.g: "5/10" fires every ten minutes after HH:05)
     *   + "*" meaning trigger will fire every minute of the hour
     * @var string
     */
    public $minutes;

    /**
     * Pattern that determines the hour portion of trigger firing
     *
     * Pattern can consist of following tokens:
     *   + single hour value between 0 and 23 these can be separated by commas to create a set
     *        (e.g: "5" OR "5,7,9,11,13")
     *   + a range of hours (e.g: "0-10" fires every minute from 00:MM to 10:MM)
     *       ranges can also be separated by commas (e.g: "0-10,12-23")
     *   + an hour value with an increment (e.g: "1/2" fires every 2 hours after 01:MM)
     *   + "*" meaning trigger will fire every hour of the day
     *
     * @var string
     */
    public $hours;

    /**
     * Pattern that determines the month portion of trigger firing
     *
     * This should be an array of zero-indexed month indices (e.g: 0 = January, 11 = December)
     *
     * Example: array("1", "10")
     * This would run the trigger in February and November
     *
     * @var array
     */
    public $months;

    /**
     * Type of days on which the trigger should fire
     *
     * Supported Values:
     *   "ALL", "WEEK", "MONTH"
     *
     * @var string
     */
    public $daysType;

    /**
     * The week days on which a trigger should fire. Use 1-indexed week days (e.g: 1 = Sunday, 7 = Saturday)
     *
     * Example: array("1", "7")
     * This would run a trigger on Sundays and Saturdays
     *
     * @var array
     */
    public $weekDays;

    /**
     * Pattern that describes the month days in which the trigger should fire.
     *
     * Pattern can consist of following tokens:
     *   + single day value between 1 and 31 these can be separated by commas to create a set
     *        (e.g: "5" OR "5,7,9,11,13")
     *   + a range of days (e.g: "1-10" fires every day from the 1st to the 10th)
     *       ranges can also be separated by commas (e.g: "1-10,15-30")
     *   + a minute value with an increment (e.g: "1/5" fires every five days after the first of the month)
     *   + "*" meaning trigger will fire every day
     * @var
     */
    public $monthDays;


    public function __construct($minutes = null, $hours = null, $daysType = null, $weekDays = null, $monthDays = null)
    {
        $this->minutes = $minutes;
        $this->hours = $hours;
        $this->daysType = $daysType;
        $this->weekDays = $weekDays;
        $this->monthDays = $monthDays;
    }

    /**
     * Overrides Trigger to provide proper JSON hierarchy as described by JRS
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $result = array();
        foreach (get_object_vars($this) as $k => $v) {
            // $v is not checked for null like other jsonSerialize functions, all values should be defined here

            // Two special cases occur because they require sublevels in their JSON
            // encoding to be considered valid.
            if ($k == "months") {
                $result[$k] = array("month" => $this->months);
            }
            else if ($k == "weekDays") {
                $result[$k] = array("day" => $this->weekDays);
            }
            else {
                $result[$k] = $v;
            }
        }
        return array($this->name() => $result);
    }

    /**
     * This function takes a \stdClass decoded by json_decode representing a scheduled job
     * and casts it as a CalendarTrigger Object
     *
     * @param \stdClass $json_obj
     * @return CalendarTrigger
     */
    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v) {
            if ($k == "months") {
                $result->$k = $v->month;
            }
            else if ($k == "weekDays") {
                $result->$k = $v->day;
            }
            else {
                $result->$k = $v;
            }
        }
        return $result;
    }

} 