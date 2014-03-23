<?php
namespace Jaspersoft\Tool;

abstract class CompositeDTOMapper {

    /**
     * The Reference Map contains a mapping of field keys to their respective
     * reference key names. This map is necessary because some of these keys cannot be
     * discerned simply by looking at the field name.
     *
     * @var array
     */
    private static $referenceMap = array(
        "dataSource" => "dataSourceReference",
        "inputControls" => "inputControlReference",
        "jrxml" => "jrxmlFileReference",
        "file" => "fileReference",
        "olapConnection" => "olapConnectionReference",
        "query" => "queryReference",
        "dataType" => "dataTypeReference",
        "listOfValues" => "listOfValuesReference",
        "schema" => "schemaReference",
        "accessGrantSchema" => "accessGrantSchemaReference",
        "mondrianConnection" => "mondrianConnectionReference",
        "securityFile" => "securityFileReference"
    );

    /**
     * Composite Field Map contains an array corresponding to each resource DTO
     * of the fields which can be considered "composite"
     *
     * This discerns between simple fields and complex fields (those which need further alteration)
     *
     * @var array
     */
    private static $compositeFieldMap = array(
        "InputControl" => array("dataType", "query", "listOfValues"),
        "MondrianConnection" => array("schema", "dataSource"),
        "MondrianXmlaDefinition" => array("mondrianConnection"),
        "OlapUnit" => array("olapConnection"),
        "Query" => array("dataSource"),
        "ReportUnit" => array("dataSource", "jrxml", "query", "inputControls"),
        "DomainTopic" => array("dataSource", "jrxml", "query", "inputControls"),
        "SecureMondrianConnection" => array("dataSource", "schema", "accessGrantSchemas"),
        "SemanticLayerDataSource" => array("schema", "dataSource", "securityFile")
    );

    /** A collection of mappings of field names for file-based resources that appear as
     * sub resources in various DTOs
     *
     * @var array
     */
    private static $fileResourceMap = array(
        "schema" => "schema",
        "accessGrantSchemas" => "accessGrantSchema",
        "jrxml" => "jrxmlFile",
        "securityFile" => "securityFile"
    );

    /** Return a value from a map given the key
     *
     * @param $field Field to be resolved
     * @param $map Map to use for resolution
     * @return string|null
     */
    private static function forwardResolve($field, $map) {
        if (array_key_exists($field, $map)) {
            return $map[$field];
        } else {
            // TODO: Appropriate Exception
            return null;
        }
    }

    /** Return the key of a map given the value
     * This method assumes the data map has a one-to-one relationship
     *
     * @param $field Field to be resolved
     * @param $map Map to use for resolution
     * @return string|null
     */
    private static function reverseResolve($field, $map) {
        $backwardMap = array_reverse($map);
        if (array_key_exists($field, $backwardMap)) {
            return $backwardMap[$field];
        } else {
            // TODO: Appropriate Exception
            return null;
        }
    }

    /** referenceKey returns the key needed for a reference of the $field's type.
     *
     * @param $field Reference Field Name
     * @return string|null
     */
    public static function referenceKey($field)
    {
        return self::forwardResolve($field, static::$referenceMap);
    }

    public static function dereferenceKey($field)
    {
        return self::reverseResolve($field, static::$referenceMap);
    }

    public static function compositeFields($class)
    {
        $className = explode('\\', $class);
        $className = end($className);

        return self::forwardResolve($className, static::$compositeFieldMap);
    }

    public static function fileResourceField($field) {
        return self::forwardResolve($field, static::$fileResourceMap);
    }

    public static function fileResourceFieldReverse($field) {
        return self::reverseResolve($field, static::$fileResourceMap);
    }

}
