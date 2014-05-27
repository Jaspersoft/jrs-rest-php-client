<?php
namespace Jaspersoft\Dto\Resource;

use Jaspersoft\Tool\CompositeDTOMapper;

if (!defined("RESOURCE_NAMESPACE")) {
    define("RESOURCE_NAMESPACE", "Jaspersoft\\Dto\\Resource");
}

/**
 * Class CompositeResource
 * @package Jaspersoft\Dto\Resource
 */
abstract class CompositeResource extends Resource
{
    protected function resolveSubresource($field, $value, $class)
    {
        if (is_string($value))
        {
            // Subresource is a reference to another resource
            return array(CompositeDTOMapper::referenceKey($field, $class) => array("uri" => $value));
        } else if (is_object($value)) {
            if (is_a($value, RESOURCE_NAMESPACE . "\\File")) {
                // File-based resources can represent several types of data
                // We must find the proper field title, and use it instead of "file"
                $resolveField = CompositeDTOMapper::fileResourceFieldReverse($field, $class);
                if ($resolveField !== null) {
                    return array($resolveField => $value->jsonSerialize());
                }
            }
            // Subresource is locally defined, and not a special file-based subresource
            return array($value->name() => $value->jsonSerialize());
        } else if (is_array($value)) {
            if (array_key_exists(0, $value)) {
                $resourceCollection = array();
                // Subresource is a collection of other resources which may or may not be references/local definitions
                if (CompositeDTOMapper::isCollectionField($field, $class)) {
                    $pair = CompositeDTOMapper::collectionKeyValuePair($class, $field);
                    foreach ($value as $k => $v) {
                        $resourceCollection[] = $this->resolveSubresource($k, $v, $class) + array($pair[0] => $k);
                    }
                } else {
                    foreach ($value as $k => $v) {
                        $resourceCollection[] = $this->resolveSubresource($field, $v, $class);
                    }
                }
                return $resourceCollection;
            } else {
                // We have an associative array, and not a collection of items
                $items = array();
                foreach ($value as $k => $v) {
                    if (CompositeDTOMapper::isReferenceKey($k)) {
                        $items[$k] = $this->resolveSubresource($k, $v, $class);
                    } else if (CompositeDTOMapper::isCollectionField($field, $class)) {
                        $fileField = CompositeDTOMapper::collectionKeyValuePair($class, $field)[1];
                        $items[$k] = $this->resolveSubresource($fileField, $v, $class);
                    } else {
                        $items[$k] = $v;
                    }
                }
                if (CompositeDTOMapper::isCollectionField($field, $class)) {
                    return CompositeDTOMapper::unmapCollection($items, $class, $field);
                } else {
                    return $items;
                }
            }
        } else {
            //TODO: Add appropriate exception
            return null;
        }
    }

    protected static function synthesizeSubresource($field, $value, $class)
    {
        $expectedReferenceKey = CompositeDTOMapper::referenceKey($field, $class);

        if(array_key_exists($expectedReferenceKey, $value)) {
            // This value is a reference and should return a string
            return $value[$expectedReferenceKey]['uri'];
        } else if (array_key_exists(0, $value)) {
            // This value is an array and should return an array of elements
            $subElements = array();
            foreach ($value as $item) {
                $subElements[] = self::synthesizeSubresource($field, $item, $class);
            }

            if (CompositeDTOMapper::isCollectionField($field, $class)) {
                return CompositeDTOMapper::mapCollection($subElements, $class, $field);
            }
            else {
                return $subElements;
            }
        } else if (sizeof($value) == 1) {
            // This value is an object (local definition) and should build a new object based on this data
            $element = array_keys($value);
            $className = RESOURCE_NAMESPACE . '\\' . ucfirst(end($element));
            if (class_exists($className)) {
                return $className::createFromJSON(end($value), $className);
            } else {
                // This may be a File-based subresource (e.g: schema, accessGrantSchema...)
                $fileType = CompositeDTOMapper::fileResourceField($field, $class);
                if ($fileType != null) {
                    return File::createFromJSON(end($value), RESOURCE_NAMESPACE . "\\File");
                } else {
                    //TODO: Unknown Data Exception
                    return null;
                }
            }
        } else if (sizeof($value) > 1) {
            // If we have an array with more than one value, and the key 0 does not exist
            // we can assume this is an associative array derived from a definition with more than one field

            $items = array();
            foreach ($value as $k => $v) {
                if (CompositeDTOMapper::isReferenceKey($k)) {
                    $items[$k] = self::synthesizeSubresource($k, $v, $class);
                } else {
                    $items[$k] = $v;
                }
            }
            return $items;
        } else {
            // TODO: Unknown Data Exception
            return null;
        }
    }

    /** This function combines non-composite resources with the proper representation of composite resources
     * into a data array which can be encoded by json_encode() creating a valid request for the Report Server
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $allFields = parent::jsonSerialize();
        $compositeFields = CompositeDTOMapper::compositeFields(get_class($this));
        foreach ($compositeFields as $key) {
            if (isset($this->$key)) {
                $allFields[$key] = $this->resolveSubresource($key, $this->$key, $this->name());
            }
        }
        return $allFields;
    }

    /** This function takes an array of elements provided by a decoded response from the server and uses
     * it to create a DTO representing the resource being deserialized.
     *
     *
     * @param $json_data array
     * @param $type string The class type to be created
     * @return array
     */
    public static function createFromJSON($json_data, $type = null)
    {
        $className = explode('\\', $type);
        $className = lcfirst(end($className));

        $allFields = parent::createFromJSON($json_data, $type);
        $compositeFields = CompositeDTOMapper::compositeFields(get_class($allFields));
        foreach ($compositeFields as $key) {
            if (isset($allFields->$key)) {
                $allFields->$key = self::synthesizeSubresource($key, $json_data[$key], $className);
            }
        }
        return $allFields;

    }
}