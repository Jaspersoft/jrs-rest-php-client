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
        "SecureMondrianConnection" => array("dataSource", "schema", "accessGrantSchemas"),
        "SemanticLayerDataSource" => array("schema", "dataSource", "securityFile")
    );

    /** referenceKey returns the key needed for a reference of the $field's type.
     *
     * @param $field
     * @return null
     */
    public static function referenceKey($field)
    {
        if (array_key_exists($field, static::$referenceMap)) {
            return static::$referenceMap[$field];
        } else {
            //TODO: Add appropriate exception
            return null;
        }
    }

    public static function dereferenceKey($field)
    {
        $backwardMap = array_reverse(static::$referenceMap);
        if (array_key_exists($field, $backwardMap)) {
            return $backwardMap[$field];
        } else {
            // TODO: Add appropriate exception
            return null;
        }
    }

    public static function compositeFields($class)
    {
        $className = explode('\\', $class);
        // Strict standards require end to only take variables, not functions.
        $className = end($className);

        if (array_key_exists($className, static::$compositeFieldMap)) {
            return static::$compositeFieldMap[$className];
        } else {
            //TODO: Add appropriate exception
            return null;
        }
    }

}