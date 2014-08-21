<?php


namespace Jaspersoft\Dto\Job\Calendar;

/**
 * Subclasses of this class represent calendar objects that describe excluded dates by an array of string values in a
 * YYYY-MM-DD format
 *
 * Class DatedCalendar
 * @package Jaspersoft\Dto\Job\Calendar
 */
abstract class DatedCalendar extends BaseCalendar {

    public $excludeDays;

    /**
     * Add a date to the exclude list
     *
     * @param string $date YYYY-MM-DD
     */
    public function addExcludeDate($date) {
        $this->excludeDays[] = $date;
    }

    /**
     * Add multiple dates to the exclude list
     *
     * @param array $dates Set of strings in format: YYYY-MM-DD
     */
    public function addExcludeDates(array $dates) {
        foreach ($dates as $date) {
            $this->addExcludeDate($date);
        }
    }

    /**
     * Remove a date from the exclude list
     *
     * @param string $date YYYY-MM-DD
     */
    public function removeExcludeDate($date) {
        $key = array_search($date, $this->excludeDays);
        if (!($key === False)) {
            unset($this->excludeDays[$key]);
            $this->excludeDays = array_values($this->excludeDays);
        }
    }

    /**
     * Remove multiple dates from the exclude list
     *
     * @param array $dates Set of strings in format: YYYY-MM-DD
     */
    public function removeExcludeDates(array $dates) {
        foreach ($dates as $date) {
            $this->removeExcludeDate($date);
        }
    }

    public static function createFromJSON($json_obj) {
        $pre = parent::createFromJSON($json_obj);
        // Hide this nesting from user
        if (!empty($pre->excludeDays->excludeDay)) {
            $pre->excludeDays = $pre->excludeDays->excludeDay;
        }
        return $pre;
    }

    public function jsonSerialize() {
        $pre = parent::jsonSerialize();
        // Return hidden nesting for supplying to server
        $pre['excludeDays'] = (!empty($pre['excludeDays'])) ? array("excludeDay" => $this->excludeDays) : null;
        return $pre;
    }

} 