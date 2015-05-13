<?php


namespace Jaspersoft\Dto\Attribute;


use Jaspersoft\Dto\DTOObject;

/**
 * Represents a user attribute
 *
 * @package Jaspersoft\Dto\Attribute
 */
class Attribute extends DTOObject {

    public $name;
    public $value;
    public $description;
    public $secure;
    public $inherited;
    public $permissionMask;
    public $holder;

    public function __construct($name = null, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

}
