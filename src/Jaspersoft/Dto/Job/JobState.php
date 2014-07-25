<?php
namespace Jaspersoft\Dto\Job;

use Jaspersoft\Dto\DTOObject;

class JobState extends DTOObject
{

    /**
     * A timestamp of the last time the job was ran
     *
     * @var string
     */
    public $previousFireTime;

    /**
     * A timestamp of the next time the job is expected to run
     *
     * @var string
     */
    public $nextFireTime;

    /**
     * The status of the job
     *
     *     Possible Values:
     *          "NORMAL" - The job is running as expected
     *          "PAUSED" - The job has been paused and will not execute
     *
     * @var string
     */
    public $value;
    
    public function __construct($previousFireTime = null, $nextFireTime = null, $value = null)
    {
        $this->previousFireTime = $previousFireTime;
        $this->nextFireTime = $nextFireTime;
        $this->value = $value;
    }

    public static function createFromJSON($json_obj)
    {
        if (!isset($json_obj->previousFireTime))
            $json_obj->previousFireTime = null;
        if (!isset($json_obj->nextFireTime))
            $json_obj->nextFireTime = null;
        if (!isset($json_obj->value))
            $json_obj->value = null;

        return new self($json_obj->previousFireTime, $json_obj->nextFireTime, $json_obj->value);
    }
    
}