<?php
namespace Jaspersoft\Dto\Resource;
use Jaspersoft\Dto\DTOObject;

/**
 * Class Resource
 * @package Jaspersoft\Dto\Resource
 */
class Resource extends DTOObject
{
    public $uri;
    public $label;
    public $description;
    public $permissionMask;
    public $creationDate;
    public $updateDate;
    public $version;

    public static function createFromJSON($json_data, $type = null)
    {
        $result = (empty($type)) ? new self : new $type();
        foreach($json_data as $k => $v)
            $result->$k = $v;
        return $result;
    }

    public function jsonSerialize()
    {
        $result = array();
        foreach (get_object_vars($this) as $k => $v)
            if (isset($v))  // version errors with !empty
                $result[$k] = $v;
        return $result;
    }

    public function __construct()
    {

    }

    public function name()
    {
        $type = explode('\\', get_class($this));
        $type = lcfirst(end($type));
        return $type;
    }

    public static function className()
    {
        $type = explode('\\', get_called_class());
        $type = lcfirst(end($type));
        return $type;
    }

    public function contentType()
    {
        return "application/repository.".$this->name()."+json";
    }
}
