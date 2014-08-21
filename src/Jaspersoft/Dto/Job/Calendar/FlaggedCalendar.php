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
     *  0 - 6 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 6)
     *
     * @param integer $day
     */
    public function addExcludeDay($day) {
        $this->excludeDaysFlags[] = $day;
    }

    /**
     * Add multiple dates to the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 6 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 6)
     *
     * @param array $days
     */
    public function addExcludeDays(array $days) {
        foreach ($days as $day) {
            $this->addExcludeDay($day);
        }
    }

    /**
     * Remove a date from the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 6 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 6)
     *
     * @param integer $day
     */
    public function removeExcludeDay($day) {
        $key = array_search($day, $this->excludeDaysFlags);
        if (!($key === False)) {
            unset($this->excludeDaysFlags[$key]);
        }
    }

    /**
     * Remove multiple dates from the exclude list by number, such that:
     *  1 - 31 represent days of the month (for monthly calendar)
     *  0 - 6 represent SUN, MON, TUE, WED, THURS, FRI, SAT (SUN = 0, SAT = 6)
     *
     * @param array $days
     */
    public function removeExcludeDays(array $days) {
        foreach ($days as $day) {
            $this->removeExcludeDay($day);
        }
    }

    abstract protected function generateFlagArray();

    public function jsonSerialize() {
        $pre = parent::jsonSerialize();
        if (!empty($this->excludeDaysFlags)) {
            $pre['excludeDaysFlags'] = array("excludeDayFlag" => $this->generateFlagArray());
        }

        return $pre;
    }
} 