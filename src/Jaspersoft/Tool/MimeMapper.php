<?php
namespace Jaspersoft\Tool;

abstract class MimeMapper
{

    private static $mimeMap = array(
        "img" => "image/*",
        "font" => "font/*",
        "pdf" => "application/pdf",
        "html" => "text/html",
        "xls" => "application/xls",
        "rtf" => "application/rtf",
        "csv" => "text/csv",
        "odt" => "application/vnd.oasis.opendocument.text",
        "txt" => "text/plain",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "ods" => "application/vnd.oasis.opendocument.spreadsheet",
        "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "jrxml" => "application/jrxml",
        "jar" => "application/jar",
        "prop" => "application/properties",
        "jrtx" => "application/jrtx",
        "xml" => "application/xml",
        "css" => "text/css",
        "accessGrantSchema" => "application/accessGrantSchema",
        "olapMondrianSchema" => "application/olapMondrianSchema"
    );

    public static function mapType($jrsType)
    {
        if (array_key_exists($jrsType, static::$mimeMap)) {
            return static::$mimeMap[$jrsType];
        } else {
            return $jrsType;
        }
    }
}