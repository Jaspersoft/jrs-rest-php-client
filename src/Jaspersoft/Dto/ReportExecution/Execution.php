<?php
namespace Jaspersoft\Dto\ReportExecution;

/**
 * ** NOT IN USE **
 * This class is NOT currently being utilized, but is in place for future implementation of ReportExecutions service.
 *
 * Class Execution
 * @package Jaspersoft\Dto\ReportExecution
 */
class Execution
{

    public $status;
    public $totalPages;
    public $currentPage;
    public $errorDescriptor;
    public $reportURI;
    public $requestId;
    public $exports;

    public static function createFromJSON($json_data)
    {
        $data = json_decode($json_data, true);
        $result = new self();
        foreach ($data as $k => $v)
            $result->$k = $v;
        return $result;
    }


}