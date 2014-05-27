<?php
namespace Jaspersoft\Dto\Resource;

use Jaspersoft\Tool\DTOMapper;

/**
 * Class CollectiveResource
 *
 * A CollectiveResource is a Resource DTO which is not composite, but features an element which is a collection of
 * elements which needs to be mapped to specific key/value pairs.
 *
 * @package Jaspersoft\Dto\Resource
 */
abstract class CollectiveResource extends Resource
{

    public function jsonSerialize()
    {
        $parent = parent::jsonSerialize();
        foreach (DTOMapper::collectionFields($this->name()) as $field) {
            if (!empty($parent[$field])) {
                $parent[$field] = DTOMapper::unmapCollection($parent[$field], $this->name(), $field);
            }
        }
        return $parent;
    }

    public static function createFromJSON($json_data, $type = null)
    {
        $class = self::className();
        $parent = parent::createFromJSON($json_data, get_called_class());
        foreach (DTOMapper::collectionFields($class) as $field) {
            if (!empty($parent->$field)) {
                $parent->$field = DTOMapper::mapCollection($parent->$field, $class, $field);
            }
        }
        return $parent;
    }

} 