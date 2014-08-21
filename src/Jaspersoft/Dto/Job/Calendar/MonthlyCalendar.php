<?php


namespace Jaspersoft\Dto\Job\Calendar;


class MonthlyCalendar extends FlaggedCalendar {

    const MONTHLY_FILL_INDEX = 31;
    public $calendarType = "monthly";


    public function addExcludeDay($day)
    {
        if (!is_int($day) || $day < 1 || $day > 31) {
            throw new \DomainException("You must describe days of the month using integers between 1 and 31");
        }
        else {
            parent::addExcludeDay($day);
        }
    }

    public function removeExcludeDay($day)
    {
        if (!is_int($day) || $day < 1 || $day > 31) {
            throw new \DomainException("You must describe days of the month using integers between 1 and 31");
        }
        else {
            parent::removeExcludeDay($day);
        }
    }


    protected function generateFlagArray()
    {
        $flagArray = array_fill(0, MonthlyCalendar::MONTHLY_FILL_INDEX, false);
        foreach ($this->excludeDaysFlags as $key) {
            $flagArray[$key - 1] = true;
        }
        return $flagArray;
    }
}