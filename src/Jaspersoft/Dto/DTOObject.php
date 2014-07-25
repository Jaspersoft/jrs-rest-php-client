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

    /**
     * Get the name of this class if it were a key field in a JSON representation
     *
     * @return string
     */
    public static function jsonField($plural = false)
    {
        $field = explode('\\', get_called_class());
        $field = lcfirst(end($field));
        return ($plural) ? $field : $field . "s";
    }

} 