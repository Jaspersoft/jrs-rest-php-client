<?php

namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Dto\ReportExecution\Request;
use Jaspersoft\Dto\ReportExecution\ReportExecution;
use Jaspersoft\Dto\ReportExecution\Status;
use Jaspersoft\Exception\ReportExecutionException;
use Jaspersoft\Exception\RESTRequestException;
use Jaspersoft\Service\Criteria\ReportExecutionSearchCriteria;
use Jaspersoft\Service\Result\SearchReportExecutionResults;

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

    private function makeUrl($id = null, $status = false)
    {
        $result = $this->base_url . '/reportExecutions';
        if (!empty($id)) {
            $result .= '/' . $id;
        }
        if ($status) {
            $result .= '/status';
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
     * If a report execution is already running, you can cancel the execution using this method
     *
     * @param ReportExecution $reportExecution
     * @return Status
     * @throws \Exception
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
                throw new ReportExecutionException(ReportExecutionException::REPORT_COMPLETE_OR_NOT_FOUND, $e);
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
     * @param $executionId
     * @return ReportExecution
     */
    public function getReportExecutionDetails($executionId)
    {
        $url = $this->makeUrl($executionId);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);

        return ReportExecution::createFromJSON(json_decode($response));
    }

    public function searchReportExecutions(ReportExecutionSearchCriteria $criteria)
    {
        $url = $this->makeUrl() . '?' . $criteria->toQueryParams();
        echo $url;
        try {
            $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        } catch (RESTRequestException $e) {
            if ($e->statusCode == 204) {
                throw new ReportExecutionException(ReportExecutionException::SEARCH_NO_RESULTS, $e);
            } else {
                throw $e;
            }
        }
        return SearchReportExecutionResults::createFromJSON($response);
    }



}