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

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

}