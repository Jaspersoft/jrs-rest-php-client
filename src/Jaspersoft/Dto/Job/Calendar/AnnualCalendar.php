<?php


namespace Jaspersoft\Dto\Job\Calendar;


class AnnualCalendar extends DatedCalendar {

    public $excludeDays;

    public static function createFromJSON($json_obj) {
        $pre = parent::createFromJSON($json_obj);
        // Hide this nesting from user
        $pre->excludeDays = (!empty($pre->excludeDays)) ? $pre->excludeDays->excludeDay : null;
    }

    public function jsonSerialize() {
        $pre = parent::jsonSerialize();
        // Return hidden nesting for supplying to server
        $pre->excludeDays = (!empty($pre->excludeDays)) ? array("excludeDay" => $pre->excludeDays) : null;
    }
} 