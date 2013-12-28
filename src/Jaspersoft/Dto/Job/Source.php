<?php

namespace Jaspersoft\Dto\Job;


class Source {

    /** URI of the report unit or report options to schedule
     * @var string
     */
    public $reportUnitURI;

    /** A set of input control parameters in the format "key" => "value" where value is an array of selected options.
     *
     * Example: array("Country_multi_select" => array("Mexico", "US", "Bolivia"), "Cascading_name_single_select" =>
     *      array("Engineering Lab", "Architecture Department"))
     *
     * @var array
     */
    public $parameters = array();

    public function jsonSerialize() {
        $result = array();
        if (isset($this->reportUnitURI)) {
            $result["reportUnitURI"] = $this->reportUnitURI;
        }

        if (isset($this->parameters)) {
            $result["parameters"] = array("parameterValues" => $this->parameters);
        }

        return $result;
    }

} 