<?php
namespace Jaspersoft\Dto\Resource;

/**
 * Class ReportOptions
 * @package Jaspersoft\Dto\Resource
 */
class ReportOptions extends CollectiveResource
{
    public $reportUri;
    public $reportParameters;

    /** Add a parameter to the report option
     *
     * @param $name string the name of the parameter
     * @param $value array an array of the selected values for the parameter
     */
    public function addParameter($name, $value) {
        $this->reportParameters[] = array("name" => $name, "value" => $value);
    }
}
