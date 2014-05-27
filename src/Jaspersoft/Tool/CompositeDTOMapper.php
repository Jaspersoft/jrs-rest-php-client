<?php
namespace Jaspersoft\Tool;

abstract class CompositeDTOMapper extends DTOMapper
{

    /**
     * The Reference Map contains a mapping of field keys to their respective
     * reference key names. This map is necessary because some of these keys cannot be
     * discerned simply by looking at the field name.
     *
     * semanticLayerDataSource is defined separately because it utilizes the schema field but refers to schema
     * references using "schemaFileReference" instead of "schemaReference" as utilized by mondrianConnection and
     * secureMondrianConnection.
     *
     *
     * @var array
     */
    private static $referenceMap = array(
        "default" => array(
            "dataSource" => "dataSourceReference",
            "inputControls" => "inputControlReference",
            "jrxml" => "jrxmlFileReference",
            "file" => "fileReference",
            "olapConnection" => "olapConnectionReference",
            "query" => "queryReference",
            "dataType" => "dataTypeReference",
            "listOfValues" => "listOfValuesReference",
            "schema" => "schemaReference",
            "accessGrantSchemas" => "accessGrantSchemaReference",
            "mondrianConnection" => "mondrianConnectionReference",
            "securityFile" => "securityFileReference"
        ),
        "semanticLayerDataSource" => array(
            "schema" => "schemaFileReference",
            "securityFile" => "securityFileReference",
            "dataSource" => "dataSourceReference",
            "file" => "fileReference"
        )
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
        "ReportUnit" => array("dataSource", "jrxml", "query", "inputControls", "resources"),
        "DomainTopic" => array("dataSource", "jrxml", "query", "inputControls", "resources"),
        "SecureMondrianConnection" => array("dataSource", "schema", "accessGrantSchemas"),
        "SemanticLayerDataSource" => array("schema", "dataSource", "securityFile", "bundles")
    );

    /** A collection of mappings of field names for file-based resources that appear as
     * sub resources in various DTOs
     *
     * @var array
     */
    private static $fileResourceMap = array(
        "default" => array(
            "schema" => "schema",
            "accessGrantSchemas" => "accessGrantSchema",
            "jrxml" => "jrxmlFile",
            "securityFile" => "securityFile",
            "file" => "fileResource"
        ),
        "semanticLayerDataSource" => array(
            "schema" => "schemaFile",
            "securityFile" => "securityFile",
            "file" => "file"
        )
    );


    /** Return a value from a map given the key
     *
     * @param $field Field to be resolved
     * @param $map Map to use for resolution
     * @return string|null
     */
    private static function forwardResolve($field, $map)
    {
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
    private static function reverseResolve($field, $map)
    {
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
     * The class parameter should only be needed so far in one special case, where the schema reference must be
     * distinguished by its class name:
     *
     *      secureMondrianConnection/mondrianConnection: schema -> schemaReference
     *      semanticLayerDataSource: schema -> schemaFileReference
     *
     * @param $field Reference Field Name
     * @param $class string Name of the class to obtain reference for
     * @return string|null
     */
    public static function referenceKey($field, $class = null)
    {
        if (!empty($class) and array_key_exists($class, static::$referenceMap)) {
            return self::forwardResolve($field, static::$referenceMap[$class]);
        } else {
            return self::forwardResolve($field, static::$referenceMap["default"]);
        }
    }

    public static function dereferenceKey($field, $class = null)
    {
        if (!empty($class) and array_key_exists($class, static::$referenceMap)) {
            return self::reverseResolve($field, static::$referenceMap[$class]);
        } else {
            return self::reverseResolve($field, static::$referenceMap["default"]);
        }
    }

    /** Returns a boolean value stating whether the field is recognized as a reference key or not.
     *
     * @param $field resource field name
     * @return boolean
     */
    public static function isReferenceKey($field)
    {
        return array_key_exists($field, static::$referenceMap["default"]);
    }

    public static function compositeFields($class)
    {
        $className = explode('\\', $class);
        $className = end($className);

        return self::forwardResolve($className, static::$compositeFieldMap);
    }

    public static function fileResourceField($field, $class = null)
    {
        if (!empty($class) and array_key_exists($class, static::$fileResourceMap)) {
            return self::forwardResolve($field, static::$fileResourceMap[$class]);
        } else {
            return self::forwardResolve($field, static::$fileResourceMap["default"]);
        }
    }

    public static function fileResourceFieldReverse($field, $class = null)
    {
        if (!empty($class) and array_key_exists($class, static::$fileResourceMap)) {
            return self::reverseResolve($field, static::$fileResourceMap[$class]);
        } else {
            return self::reverseResolve($field, static::$fileResourceMap["default"]);
        }
    }

}
