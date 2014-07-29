<?php
/**
 * @author Grant Bacon (gbacon@jaspersoft.com)
 */

namespace Jaspersoft\Dto\ReportExecution;


use Jaspersoft\Dto\DTOObject;

class Parameter extends DTOObject {

    /**
     * Name of the input control (parameter)
     *
     * @var string
     */
    public $name;

    /**
     * Value(s) of the input control (parameter)
     *
     * @var array
     */
    public $value;

    public function jsonSerialize()
    {
        return array("name" => $this->name, "value" => (array) $this->value);
    }

} 