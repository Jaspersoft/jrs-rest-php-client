<?php

namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Dto\ReportExecution\Request;
use Jaspersoft\Dto\ReportExecution\ReportExecution;
use Jaspersoft\Dto\ReportExecution\Status;
use Jaspersoft\Exception\ReportExecutionException;
use Jaspersoft\Exception\RESTRequestException;

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
        if ($status) {
            $result .= '/' . $id . '/status';
        }
        return $result;
    }

    public function runReportExecution(Request $request)
    {
        $url = $this->makeUrl();
        $response = $this->service->prepAndSend($url, array(200), 'POST', $request->toJSON(), true);

        return ReportExecution::createFromJSON(json_decode($response));
    }

    public function getReportExecutionStatus(ReportExecution $reportExecution)
    {
        $url = $this->makeUrl($reportExecution->requestId, true);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true, "application/json", "application/status+json");

        return Status::createFromJSON(json_decode($response));
    }
    
    public function cancelReportExecution(ReportExecution $reportExecution)
    {
        $url = $this->makeUrl($reportExecution->requestId, true); // cancel happens at status endpoint
        try {
            $response = $this->service->prepAndSend($url, array(200), 'PUT', json_encode(array("value" => "cancelled")), true);
        } catch (RESTRequestException $e) {
            if ($e->statusCode == 204) {
                throw new ReportExecutionException(ReportExecutionException::REPORT_COMPLETE_OR_NOT_FOUND);
            } else {
                throw $e;
            }
        }

        return $response;
    }

}