<?php

namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Dto\ReportExecution\Parameter;
use Jaspersoft\Dto\ReportExecution\Request;
use Jaspersoft\Dto\ReportExecution\ReportExecution;
use Jaspersoft\Dto\ReportExecution\Status;
use Jaspersoft\Exception\DtoException;
use Jaspersoft\Exception\ReportExecutionException;
use Jaspersoft\Exception\RESTRequestException;
use Jaspersoft\Service\Criteria\ReportExecutionSearchCriteria;
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

    private function makeUrl($id = null, $status = false, $parameters = false)
    {
        $result = $this->base_url . '/reportExecutions';
        if (!empty($id)) {
            $result .= '/' . $id;
        }
        if ($status) {
            $result .= '/status';
        } else if ($parameters) {
            $result .= '/parameters';
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
     * @param $executionId
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


}