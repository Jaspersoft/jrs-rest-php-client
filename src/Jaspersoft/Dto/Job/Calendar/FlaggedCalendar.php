<?php


namespace Jaspersoft\Dto\Job\Calendar;

/**
 * Classes which extend this class are calendar objects which include flagged date exclusions (arrays of boolean values corresponding
 * to days of a month or week)
 *
 * Class FlaggedCalendar
 * @package Jaspersoft\Dto\Job\Calendar
 */
abstract class FlaggedCalendar extends BaseCalendar {

    public $excludeDaysFlags;

    /**
     * Add a date to the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 7 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 7)
     *
     * @param integer $day
     */
    public function addExcludeDay($day) {

    }

    /**
     * Add multiple dates to the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 7 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 7)
     *
     * @param array $days
     */
    public function addExcludeDays(array $days) {

    }

    /**
     * Remove a date from the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 7 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 7)
     *
     * @param integer $day
     */
    public function removeExcludeDay($day) {

    }

    /**
     * Remove multiple dates from the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 7 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 7)
     *
     * @param array $days
     */
    public function removeExcludeDays(array $days) {

    }
} 