<?php
namespace Jaspersoft\Dto\Resource;

/**
 * Class ResourceLookup
 * @package Jaspersoft\Dto\Resource
 */
class ResourceLookup
{

    public $uri;
    public $label;
    public $description;
    public $resourceType;
    public $permissionMask;
    public $version;
    public $creationDate;
    public $updateDate;

    public function __construct()
    {

    }

    public function jsonSerialize()
    {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v))
                $data[$k] = $v;
        }
        return $data;
    }

    public static function createFromJSON($json_data)
    {
        $temp = new self;
        foreach ($json_data as $k => $v)
            $temp->$k = $v;
        return $temp;
    }

}