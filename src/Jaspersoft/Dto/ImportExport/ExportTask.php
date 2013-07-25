<?php
namespace Jaspersoft\Dto\ImportExport;

class ExportTask implements \JsonSerializable {

    public $users = array();
    public $uris = array();
    public $roles = array();
    public $parameters = array();

    public function __construct($parameters = null)
	{
        $this->parameters[] = $parameters;
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

}