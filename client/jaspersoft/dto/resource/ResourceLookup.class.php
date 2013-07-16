<?php
namespace jaspersoft\dto\resource;

class ResourceLookup implements \JsonSerializable {

    public $uri;
    public $label;
    public $description;
    public $resourceType;
    public $permissionMask;
    public $version;
    public $creationDate;
    public $updateDate;

    public function __construct() {

    }

    public function jsonSerialize() {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v))
                $data[$k] = $v;
        }
        return $data;
    }

    public static function createFromJSON($json_data) {
        $temp = new self;
        foreach ($json_data as $k => $v)
            $temp->$k = $v;
        return $temp;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $permissionMask
     */
    public function setPermissionMask($permissionMask)
    {
        $this->permissionMask = $permissionMask;
    }

    /**
     * @return mixed
     */
    public function getPermissionMask()
    {
        return $this->permissionMask;
    }

    /**
     * @param mixed $resourceType
     */
    public function setType($resourceType)
    {
        $this->type = $resourceType;
    }

    /**
     * @return mixed
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * @param mixed $updateDate
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

}

?>