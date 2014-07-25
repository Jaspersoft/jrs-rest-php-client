<?php
namespace Jaspersoft\Dto\ImportExport;
use Jaspersoft\Dto\DTOObject;

/**
 * Class TaskState
 * Describes the state of an import or export task that has been executed
 *
 * @package Jaspersoft\Dto\ImportExport
 */
class TaskState extends DTOObject
{

    /**
     * ID for the task (read-only)
     *
     * @var int
     */
    public $id;

    /**
     * Current phase of the task (read-only)
     *          'inprogress' - The task is currently being executed
     *          'finished' - The task has completed.
     *          'failed' - The task has failed to complete
     *
     * @var string
     */
    public $phase;

    /**
     * A message returned by the server in regard to the task, especially useful in the event of failure.
     *
     * @var string
     */
    public $message;

    public function __construct($id = null, $phase = null, $message = null)
    {
        $this->id = $id;
        $this->phase = $phase;
        $this->message = $message;
    }

    public static function createFromJSON($json_obj)
    {
        return new self($json_obj->id, $json_obj->phase, $json_obj->message);
    }

}