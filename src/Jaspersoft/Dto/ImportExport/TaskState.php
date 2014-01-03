<?php


namespace Jaspersoft\Dto\ImportExport;


class TaskState {

    public $id;
    public $phase;
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