<?php

namespace Jaspersoft\Dto\ReportExecution;

use Jaspersoft\Dto\DTOObject;

class ErrorDescriptor extends DTOObject {

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $errorCode;

    /**
     * An array of strings describing the error
     *
     * @var array
     */
    public $parameters;

    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v) {
            if (!empty($v)) {
                $result->$k = $v;
            }
        }
        return $result;
    }

} 