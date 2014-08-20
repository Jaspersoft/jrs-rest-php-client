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

    }

    /**
     * Add multiple dates to the exclude list
     *
     * @param array $dates Set of strings in format: YYYY-MM-DD
     */
    public function addExcludeDates(array $dates) {

    }

    /**
     * Remove a date from the exclude list
     *
     * @param string $date YYYY-MM-DD
     */
    public function removeExcludeDate($date) {

    }

    /**
     * Remove multiple dates from the exclude list
     *
     * @param array $dates Set of strings in format: YYYY-MM-DD
     */
    public function removeExcludeDates(array $dates) {

    }

} 