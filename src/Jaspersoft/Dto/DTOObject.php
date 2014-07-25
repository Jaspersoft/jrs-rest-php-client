<?php

namespace Jaspersoft\Dto;


abstract class DTOObject {

    /**
     * Creates an array based representation of class data to be serialized
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    /**
     * Returns string of JSON serialized class data
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->jsonSerialize());
    }

    public function name()
    {
        $type = explode('\\', get_class($this));
        $type = lcfirst(end($type));
        return $type;
    }

} 