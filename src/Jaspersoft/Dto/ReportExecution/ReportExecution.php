<?php
namespace Jaspersoft\Dto\ReportExecution;
use Jaspersoft\Dto\DTOObject;

/**
 *
 * Class ReportExecution
 * @package Jaspersoft\Dto\ReportExecution
 */
class ReportExecution extends DTOObject
{

    /**
     * Status of the execution
     * @var string
     */
    public $status;

    /**
     * Number of pages in report
     *
     * @var integer
     */
    public $totalPages;

    /**
     * What page is currently being exported
     *
     * @var integer
     */
    public $currentPage;

    /**
     * Description of an error which may have occured
     *
     * @var string
     */
    public $errorDescriptor;

    /**
     * URI of Report
     *
     * @var string
     */
    public $reportURI;

    /**
     * A unique ID of execution request
     *
     * @var string
     */
    public $requestId;

    /**
     * Collection of exports and their metadata
     *
     * @var array
     */
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