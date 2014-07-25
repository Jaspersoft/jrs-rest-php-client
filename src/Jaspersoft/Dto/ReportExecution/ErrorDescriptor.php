<?php
/**
 * @author Grant Bacon (gbacon@jaspersoft.com)
 */

namespace Jaspersoft\Dto\ReportExecution;

use Jaspersoft\Dto\DTOObject;

class ErrorDescriptor extends DTOObject {

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var array
     */
    private $parameters;

    public static function createFromJSON($json_obj)
    {
        $result = new self();
        foreach ($json_obj as $k => $v)
            $result->$k = $v;
        return $result;
    }

} 