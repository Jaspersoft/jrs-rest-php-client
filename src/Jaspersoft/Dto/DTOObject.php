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
     * @param boolean $plural Suffix response with an s?
     * @return string
     */
    public static function jsonField($plural = false)
    {
        $field = explode('\\', get_called_class());
        $field = lcfirst(end($field));
        return $plural ? $field . "s" : $field;
    }

    /**
     * If an object is composed of only properties, this will handle setting those properties as they match their field
     * names. If a class expects to have subcomponents or arrays, it should be overridden.
     *
     *
     * @param \stdClass $json_obj A decoded JSON response
     * @return mixed Some type of \Jaspersoft\Dto\* object
     */
    public static function createFromJSON($json_obj)
    {
        $source_class = get_called_class();
        $result = new $source_class();

        foreach ($json_obj as $k => $v) {
            if (!empty($v)) {
                $result->$k = $v;
            }
        }
        return $result;
    }

}