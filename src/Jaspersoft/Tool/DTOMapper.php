<?php
namespace Jaspersoft\Tool;

use Jaspersoft\Exception\ResourceServiceException;

abstract class DTOMapper
{

    /** Some DTOs provide a collection of elements. This array identifies the unique key for these sets, so that the
     * array can be converted between an indexed or associative array.
     *
     * Array format:
     *      "className" => array("FIELD" => array("KEY", "VALUE"))
     *
     * @var array
     */
    protected static $collectionKeyValue = array(
        "listOfValues" => array("items" => array("label", "value")),
        "virtualDataSource" => array("subDataSources" => array("id", "uri")),
        "customDataSource" => array("properties" => array("key", "value")),
        "semanticLayerDataSource" => array("bundles" => array("locale", "file")),
        "reportUnit" => array("resources" => array("name", "file")),
        "domainTopic" => array("resources" => array("name", "file")),
        "reportOptions" => array("reportParameters" => array("name", "value"))
    );

    public static function collectionKeyValuePair($class, $field)
    {
        if (array_key_exists($field, static::$collectionKeyValue[$class])) {
            return static::$collectionKeyValue[$class][$field];
        } else {
            throw new ResourceServiceException("Unable to determine collection unique key");
        }
    }

    public static function collectionKey($class, $field)
    {
        if (array_key_exists($field, static::$collectionKeyValue[$class])) {
            return static::$collectionKeyValue[$class][$field][0];
        } else {
            throw new ResourceServiceException("Unable to determine collection unique key");
        }
    }

    public static function isCollectionField($field, $class)
    {
        return (isset(static::$collectionKeyValue[$class])) and array_key_exists($field, static::$collectionKeyValue[$class]);
    }

    public static function collectionFields($class)
    {
        return array_keys(static::$collectionKeyValue[$class]);
    }

    public static function mapCollection($indexed_array, $class, $field)
    {
        // To be used with a createFromJSON method
        $pair = self::collectionKeyValuePair($class, $field);

        $mapped_array = array();
        foreach ($indexed_array as $item) {
            $mapped_array[$item[$pair[0]]] = $item[$pair[1]];
        }
        return $mapped_array;
    }

    public static function unmapCollection($associative_array, $class, $field)
    {
        // To be used with jsonSerialize method
        $pair = self::collectionKeyValuePair($class, $field);
        $unmapped_array = array();
        foreach ($associative_array as $k => $v) {
            $unmapped_array[] = array($pair[0] => $k, $pair[1] => $v);
        }
        return $unmapped_array;
    }

} 