<?php

namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Dto\ReportExecution\Attachment;
use Jaspersoft\Dto\ReportExecution\Export\Export;
use Jaspersoft\Dto\ReportExecution\OutputResource;
use Jaspersoft\Dto\ReportExecution\Parameter;
use Jaspersoft\Dto\ReportExecution\Request;
use Jaspersoft\Dto\ReportExecution\ReportExecution;
use Jaspersoft\Dto\ReportExecution\Status;
use Jaspersoft\Exception\DtoException;
use Jaspersoft\Exception\RESTRequestException;
use Jaspersoft\Service\Criteria\ReportExecutionSearchCriteria;
use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;

/**
 * This service allows developers to get report execution metadata (execution status, total pages, errors, names of
 * attachments, etc.) as well as run reports asynchronously.
 *
 *
 * Class ReportExecutionService
 * @package Jaspersoft\Service
 */
class ReportExecutionService
{
    private $service;
    private $base_url;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->base_url = $client->getURL();
    }

    private function makeUrl($id = null, $status = false, $parameters = false, $exports = false,
                             $outputResource = false, $exportOutput = null, $attachments = false, $attachmentUri = null)
    {
        $result = $this->base_url . '/reportExecutions';
        if (!empty($id)) {
            $result .= '/' . $id;
        }
        // parameters and exports are mutually exclusive
        if ($parameters) {
            $result .= '/parameters';
        } else if ($exports) {
            $result .= '/exports';
            if (is_string($exportOutput)) {
                $result .= '/' . $exportOutput;
            }
        }
        if ($status) {
            $result .= '/status';
        }
        if ($outputResource) {
            $result .= '/outputResource';
        }
        if ($attachments) {
            $result .= "/attachments";
            if (is_string($attachmentUri)) {
                $result .= '/' . $attachmentUri;
            }
        }
        return $result;
    }

    /**
     * Submit a request to begin a report execution
     *
     * @param Request $request
     * @return ReportExecution
     */
    public function runReportExecution(Request $request)
    {
        $url = $this->makeUrl();
        $response = $this->service->prepAndSend($url, array(200), 'POST', $request->toJSON(), true);

        return ReportExecution::createFromJSON(json_decode($response));
    }

    /**
     * Obtain the status of a report execution
     *
     * @param ReportExecution $reportExecution
     * @return Status
     */
    public function getReportExecutionStatus(ReportExecution $reportExecution)
    {
        $url = $this->makeUrl($reportExecution->requestId, true);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true, "application/json", "application/status+json");

        return Status::createFromJSON(json_decode($response));
    }

    /**
     * If a report execution is already running, you can cancel the execution using this method.
     *
     * A boolean value of false will be returned in the event that the requested report has either already completed
     * or was unable to be found.
     *
     * @param ReportExecution $reportExecution
     * @return Status|bool
     * @throws \Jaspersoft\Exception\RESTRequestException
     * @throws \Jaspersoft\Exception\ReportExecutionException
     */
    public function cancelReportExecution(ReportExecution $reportExecution)
    {
        $url = $this->makeUrl($reportExecution->requestId, true);

        try {
            $response = $this->service->prepAndSend($url, array(200), 'PUT', json_encode(array("value" => "cancelled")), true);
        } catch (RESTRequestException $e) {
            if ($e->statusCode == 204) {
                return false;
            } else {
                throw $e;
            }
        }
        return Status::createFromJSON(json_decode($response));
    }

    /**
     * Obtain details about a Report Execution. Until the report is completed (or failed), this will be similar to the
     * object returned by the runReportExecution method.
     *
     * @param string $executionId
     * @return ReportExecution
     */
    public function getReportExecutionDetails($executionId)
    {
        $url = $this->makeUrl($executionId);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);

        return ReportExecution::createFromJSON(json_decode($response));
    }

    /**
     * Search report executions by URI, or Job details (id, label, user, etc.)
     *
     * @see ReportExecutionSearchCriteria
     * @param ReportExecutionSearchCriteria $criteria
     * @return array
     * @throws \Exception
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function searchReportExecutions(ReportExecutionSearchCriteria $criteria)
    {
        $url = $this->makeUrl() . '?' . $criteria->toQueryParams();
        try {
            $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        } catch (RESTRequestException $e) {
            if ($e->statusCode == 204) {
                return array();
            } else {
                throw $e;
            }
        }
        $result = array();
        $executions = json_decode($response);
        foreach ($executions->reportExecution as $reportExecution) {
            $result[] = ReportExecution::createFromJSON($reportExecution);
        }
        return $result;
    }

    /**
     * Re-run the report using new report parameters
     *
     * @see Jaspersoft\Dto\ReportExecution\Request->paramemters
     * @param ReportExecution $reportExecution
     * @param array<Jaspersoft\Dto\ReportExecution\Parameter> $newParameters An array of new reportParameters
     * @param bool $freshData Should fresh data be fetched? (Default: true)
     * @throws DtoException
     */
    public function updateReportExecutionParameters(ReportExecution $reportExecution, array $newParameters, $freshData = true)
    {
        $url = $this->makeUrl($reportExecution->requestId, false, true);
        if (is_bool($freshData)) {
            $url .= '?' . Util::query_suffix(array("freshData" => $freshData));
        }
        $parameters = array();
        foreach($newParameters as $p) {
            if ($p instanceof Parameter) {
                $parameters[] = $p->jsonSerialize();
            } else {
                throw new DtoException(get_called_class() . ": The parameter field must contain
                        only Jaspersoft\\DTO\\ReportExecution\\Parameter item(s)");
            }
        }
        $this->service->prepAndSend($url, array(204), 'POST', json_encode($parameters), true);
    }

    /**
     * Re-run an execution using new export values
     *
     * @param ReportExecution $execution
     * @param \Jaspersoft\Dto\ReportExecution\Export\Request $request
     * @return Export
     */
    public function runExportExecution(ReportExecution $execution, \Jaspersoft\Dto\ReportExecution\Export\Request $request)
    {
        $url = $this->makeUrl($execution->requestId, false, false, true);
        $response = $this->service->prepAndSend($url, array(200), 'POST', $request->toJSON(), true);

        return Export::createFromJSON(json_decode($response));
    }

    /**
     * Get the status value of an Export Execution
     *
     * @param ReportExecution $execution
     * @param Export $export
     * @return Status
     */
    public function getExportExecutionStatus(ReportExecution $execution, Export $export)
    {
        $url = $this->makeUrl($execution->requestId, true, false, true, false, $export->id);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);

        return Status::createFromJSON(json_decode($response));
    }

    /**
     * This method will download an export resource, an array is returned, one with an outputResource object that
     * describes the type of binary data, and the "body" which is the binary content of the resource.
     *
     * @param ReportExecution $execution
     * @param Export $export
     * @return array
     */
    public function getExportOutputResource(ReportExecution $execution, Export $export)
    {
        $url = $this->makeUrl($execution->requestId, false, false, true, true, $export->id);
        $response = $this->service->makeRequest($url, array(200), 'GET', null, true, 'application/json', '*/*');

        $headers = RESTRequest::splitHeaderArray($response['headers']);
        $outputResource = OutputResource::createFromHeaders($headers);

        return array("outputResource" => $outputResource, "content" => $response['body']);
    }

    /**
     * Get the binary data of an attachment for a report
     *
     * @param ReportExecution $execution
     * @param Export $export
     * @param string $attachmentName the name of the attachment (found in the fileName field of an Attachment object)
     * @return string
     */
    public function getExportOutputResourceAttachment(ReportExecution $execution, Export $export, $attachmentName)
    {
        $url = $this->makeUrl($execution->requestId, false, false, true, false, $export->id, true, $attachmentName);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', '*/*');

        return $response;
    }



}