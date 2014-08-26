<?php


namespace Jaspersoft\Dto\Domain;


use Jaspersoft\Dto\DTOObject;

class AbstractMetaEntity extends DTOObject {

    /** @var  string */
    public $id;
    /** @var  string */
    public $label;
    /** @var  array */
    public $properties;

    public static function createFromJSON($json_obj) {
        $parent = parent::createFromJSON($json_obj);
        if (!empty($parent->properties)) {
            $parent->properties = (array) $parent->properties;
        }
        return $parent;
    }


} 