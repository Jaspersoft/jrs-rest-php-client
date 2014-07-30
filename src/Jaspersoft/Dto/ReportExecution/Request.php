<?php
namespace Jaspersoft\Dto\ReportExecution;
use Jaspersoft\Dto\DTOObject;
use Jaspersoft\Exception\DtoException;
use Jaspersoft\Exception\ReportExecutionException;

/**
 * Describes a request for a Report Execution
 *
 *
 * Class ExecutionRequest
 * @package Jaspersoft\Dto\ReportExecution
 */
class Request extends DTOObject
{

    /**
     * URI of the Report to be executed
     *
     * @var string
     */
    public $reportUnitUri;

    /**
     * Should the execution take place asynchronously?
     * (server default: false)
     *
     * @var boolean
     */
    public $async;

    /**
     * Format to export report
     * (e.g: 'html', 'pdf', 'xls'...)
     *
     * @var string
     */
    public $outputFormat;

    /**
     * Should reports containing highcharts be interactive?
     * (server default: true)
     *
     * @var boolean
     */
    public $interactive;

    /**
     * Use fresh data? (as opposed to using a previously saved data snapshot)
     * (server default: false)
     *
     * @var boolean
     */
    public $freshData;

    /**
     * Should JRS save a data snapshot after fetching fresh data? (must be enabled on server)
     * (server default: false)
     *
     * @var boolean
     */
    public $saveDataSnapshot;

    /**
     * Should one long single page be generated?
     * (server default: based on JRXML for report)
     *
     * @var boolean
     */
    public $ignorePagination;

    /**
     * Used when requesting a report as a JasperPrint object
     *
     * @see net.sf.jasperreports.engine.export.GenericElementTransformer
     * @var string
     */
    public $transformerKey;

    /**
     * Pages to export, either single page or page range in format {start}-{end} (e.g: 4 or 5-9)
     *
     * @var string
     */
    public $pages;

    /**
     * A URL prefix for report attachments. (only affects HTML output)
     * This string can contain some variables which will be replaced by server at runtime:
     *   {contextPath} : the webapp directory (e.g: /jasperserver-pro)
     *   {reportExecutionId} : the ID related to this reportExecution
     *   {exportExecutionId} : the ID of the export
     *
     * @var string
     */
    public $attachmentsPrefix;

    /**
     * Settings of the report's input controls (parameters)
     *
     * @var array<Jaspersoft\Dto\ReportExecution\Parameter>
     */
    public $parameters;

    /**
     * JRS deployment URL (used to generate absolute paths to JRS Specific static resources (only affects HTML output)
     *
     * @var string
     */
    public $baseUrl;

    public function jsonSerialize()
    {
        $basic = parent::jsonSerialize();
        // Handle special wrapping case for parameters
        if (isset($this->parameters)) {
            $params = null;
            if (is_array($this->parameters)) {
                $paramSet = array();
                foreach($this->parameters as $p) {
                    if ($p instanceof Parameter) {
                        $paramSet[] = $p->jsonSerialize();
                    } else {
                        throw new DtoException(get_called_class() . ": The parameter field must contain
                        only Jaspersoft\\DTO\\ReportExecution\\Parameter item(s)");
                    }
                }
                $params = $paramSet;
            } else {
                if ($this->parameters instanceof Parameter) {
                    $params = array($this->parameters->jsonSerialize()); // Server expects array even for 1 element
                } else {
                    throw new DtoException(get_called_class() . ": The parameter field must contain
                        only Jaspersoft\\DTO\\ReportExecution\\Parameter item(s)");
                }
            }
            $basic["parameters"] = array("reportParameter" => $params);
        }
        return $basic;
    }
}