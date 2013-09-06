<?php
namespace Jaspersoft\Dto\Resource;

class Resource 
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
            if (isset($v))
                $result[$k] = $v;
        return $result;
    }

    function __construct()
    {

    }




}

?>
