<?php

namespace Jaspersoft\Service\Result;


use Jaspersoft\Dto\ReportExecution\ReportExecution;

class SearchReportExecutionResults
{
    /**
     * A collection of ReportExecution items resulting from search
     *
     * @var array
     */
    public $reportExecution;

    public static function createFromJSON($json_obj)
    {
        $result = new self();
        if (isset($json_obj->reportExecution)) {
            $set = array();
            foreach ($json_obj->reportExecution as $re) {
                $set[] = ReportExecution::createFromJSON($re);
            }
            $result->reportExecution = $set;
        }
        return $result;
    }
} 