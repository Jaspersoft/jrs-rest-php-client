<?php
namespace Jaspersoft\Dto\Resource;

use Jaspersoft\Tool\CompositeDTOMapper;
abstract class CompositeResource extends Resource {

    /** resolveSubresource discerns composite resources as reference, object, or collection.
     * It creates the appropriate underlying structure of the JSON data representation to be given to
     * the json encoder. Recursive calls are used to build collections of resources.
     *
     * @param $field
     * @param $value
     * @return array|null
     */
    protected function resolveSubresource($field, $value)
    {
        if (is_string($value))
        {
            // Subresource is a reference to another resource
            return array(CompositeDTOMapper::referenceKey($field) => array("uri" =>  $value));
        } else if (is_object($value)) {
            // Subresource is locally defined
            return array($value->name() => $value->jsonSerialize());
        } else if (is_array($value)) {
            // Subresource is a collection of other resources which may or may not be references/local definitions
            $resourceCollection = array();
            foreach ($value as $v) {
                $resourceCollection[] = $this->resolveSubresource($field, $v);
            }
            return $resourceCollection;
        } else {
            //TODO: Add appropriate exception
            return null;
        }
    }

    protected static function synthesizeSubresource($field, $value)
    {
        $expectedReferenceKey = CompositeDTOMapper::referenceKey($field);

        if(array_key_exists($expectedReferenceKey, $value)) {
            // This value is a reference and should return a string
            return $value[$expectedReferenceKey]['uri'];
        } else if (array_key_exists(0, $value)) {
            // This value is an array and should return an array of elements
            $subElements = array();
            foreach ($value as $item) {
                $subElements[] = self::synthesizeSubresource($field, $item);
            }
            return $subElements;
        } else if (sizeof($value) == 1) {
            // This value is an object (local definition) and should build a new object based on this data
            $element = array_keys($value);
            $className = RESOURCE_NAMESPACE . '\\' . ucfirst(end($element));

            return $className::createFromJSON(end($value), $className);
        } else {
            // TODO: Throw Exception for unknown data
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
                $allFields[$key] = $this->resolveSubresource($key, $this->$key);
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
        $allFields = parent::createFromJSON($json_data, $type);
        $compositeFields = CompositeDTOMapper::compositeFields(get_class($allFields));
        foreach ($compositeFields as $key) {
            if (isset($allFields->$key)) {
                $allFields->$key = self::synthesizeSubresource($key, $json_data[$key]);
            }
        }
        return $allFields;

    }

}