<?php

namespace Jaspersoft\Service;

use Jaspersoft\Dto\ReportExecution\Attachment;
use Jaspersoft\Dto\ReportExecution\BinaryOutputResource;
use Jaspersoft\Dto\ReportExecution\Export\Export;
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
class ReportExecutionService extends JRSService
{

    private function makeUrl($id = null, $status = false, $parameters = false, $exports = false,
                             $outputResource = false, $exportOutput = null, $attachments = false, $attachmentUri = null)
    {
        $result = $this->service_url . '/reportExecutions';
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
     * @param string $executionId
     * @return Status
     */
    public function getReportExecutionStatus($executionId)
    {
        $url = $this->makeUrl($executionId, true);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true, "application/json", "application/status+json");

        return Status::createFromJSON(json_decode($response));
    }

    /**
     * If a report execution is already running, you can cancel the execution using this method.
     *
     * A boolean value of false will be returned in the event that the requested report has either already completed
     * or was unable to be found.
     *
     * @param string $executionId
     * @return Status|bool
     * @throws \Jaspersoft\Exception\RESTRequestException
     * @throws \Jaspersoft\Exception\ReportExecutionException
     */
    public function cancelReportExecution($executionId)
    {
        $url = $this->makeUrl($executionId, true);

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
     * @param string $executionId
     * @param array<Jaspersoft\Dto\ReportExecution\Parameter> $newParameters An array of new reportParameters
     * @param bool $freshData Should fresh data be fetched? (Default: true)
     * @throws DtoException
     */
    public function updateReportExecutionParameters($executionId, array $newParameters, $freshData = true)
    {
        $url = $this->makeUrl($executionId, false, true);
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
     * @param string $executionId
     * @param \Jaspersoft\Dto\ReportExecution\Export\Request $request
     * @return Export
     */
    public function runExportExecution($executionId, \Jaspersoft\Dto\ReportExecution\Export\Request $request)
    {
        $url = $this->makeUrl($executionId, false, false, true);
        $response = $this->service->prepAndSend($url, array(200), 'POST', $request->toJSON(), true);

        return Export::createFromJSON(json_decode($response));
    }

    /**
     * Get the status value of an Export Execution
     *
     * @param string $executionId
     * @param string $exportId
     * @return Status
     */
    public function getExportExecutionStatus($executionId, $exportId)
    {
        $url = $this->makeUrl($executionId, true, false, true, false, $exportId);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);

        return Status::createFromJSON(json_decode($response));
    }

    /**
     * This method will download an export resource, an array is returned, one with an outputResource object that
     * describes the type of binary data, and the "body" which is the binary content of the resource.
     *
     * @param string $executionId
     * @param string $exportId
     * @return array
     */
    public function getExportOutputResource($executionId, $exportId)
    {
        $url = $this->makeUrl($executionId, false, false, true, true, $exportId);
        $response = $this->service->makeRequest($url, array(200), 'GET', null, true, 'application/json', '*/*');

        $headers = RESTRequest::splitHeaderArray($response['headers']);

        $outputResource = BinaryOutputResource::createFromHeaders($headers);
        $outputResource->binaryContent = $response['body'];

        return $outputResource;
    }

    /**
     * Get the binary data of an attachment for a report
     *
     * @param string $executionId
     * @param string $exportId
     * @param string $attachmentName the name of the attachment (found in the fileName field of an Attachment object)
     * @return string
     */
    public function getExportOutputResourceAttachment($executionId, $exportId, $attachmentName)
    {
        $url = $this->makeUrl($executionId, false, false, true, false, $exportId, true, $attachmentName);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', '*/*');

        return $response;
    }



}