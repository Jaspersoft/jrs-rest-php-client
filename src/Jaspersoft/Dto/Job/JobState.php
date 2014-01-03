<?php


namespace Jaspersoft\Dto\Job;


class JobState {

    public $previousFireTime;
    public $nextFireTime;
    public $value;
    
    public function __construct($previousFireTime = null, $nextFireTime = null, $value = null)
    {
        $this->previousFireTime = $previousFireTime;
        $this->nextFireTime = $nextFireTime;
        $this->value = $value;
    }

    public static function createFromJSON($json_obj)
    {
        return new self($json_obj->previousFireTime, $json_obj->nextFireTime, $json_obj->value);
    }
    
}