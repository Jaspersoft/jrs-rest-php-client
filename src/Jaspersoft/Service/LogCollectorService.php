<?php

namespace Jaspersoft\Service;


use Jaspersoft\Dto\Diagnostic\LogCollectorSettings;
use Jaspersoft\Exception\MissingValueException;
use Jaspersoft\Exception\RESTRequestException;

/**
 * Class LogCollectorService
 *
 * @package Jaspersoft\Service
 */
class LogCollectorService extends JRSService
{

    private function makeUrl($id = null, $download = false)
    {
        $result = $this->service_url . '/diagnostic/collectors';
        if (isset($id)) {
            $result .= "/" . $id;
        }
        if ($download) {
            $result .= "/content";
        }
        return $result;
    }

    /**
     * Create and start a diagnostic log collector
     *
     * @param \Jaspersoft\Dto\Diagnostic\LogCollectorSettings $collector
     * @return \Jaspersoft\Dto\Diagnostic\LogCollectorSettings Representation of created log collector
     */
    public function createLogCollector(LogCollectorSettings $collector)
    {
        $url = self::makeUrl();
        $jsonData = json_encode($collector->jsonSerialize());
        
        $result = $this->service->prepAndSend($url, array(200), "POST", $jsonData, true);
        $resultObject = json_decode($result);
        return LogCollectorSettings::createFromJSON($resultObject);
    }

    /**
     * Obtain metadata about all Log Collectors
     *
     * @throws RESTRequestException
     * @return array A set of LogCollectorSettings objects defining existing log collector states
     */
    public function logCollectorStates()
    {
        $url = self::makeUrl();
        $result = array();
        try {
            $response = $this->service->prepAndSend($url, array(200), "GET", null, true);
            $responseObject = json_decode($response);

            foreach ($responseObject->CollectorSettingsList as $lcs) {
                $result[] = LogCollectorSettings::createFromJSON($lcs);
            }

            /*
             * For now a facade has been created over this REST endpoint to return an empty array
             * in the case that we get a 404 with the message "Resource collectors not found"
             */
        } catch (RESTRequestException $e) {
            if ($e->message == "Resource collectors not found" && $e->statusCode == 404) {
                return $result;
            } else {
                throw $e;
            }
        }
        return $result;
    }

    /**
     * Obtain metadata about a specific Log Collector
     *
     * @param $id
     * @return \Jaspersoft\Dto\Diagnostic\LogCollectorSettings
     */
    public function logCollectorState($id)
    {
        $url = self::makeUrl($id);

        $response = $this->service->prepAndSend($url, array(200), "GET", null, true);
        $responseObject = json_decode($response);

        return LogCollectorSettings::createFromJSON($responseObject);
    }

    /**
     * Download the content of a Log Collector as a zip file
     *
     * @param $id
     * @return string Binary content of zip file
     */
    public function downloadLogCollectorContentZip($id)
    {
        $url = self::makeUrl($id, true);
        $response = $this->service->prepAndSend($url, array(200), "GET", null, true, "application/json", "application/zip");

        return $response;
    }

    /**
     * Download the content of all Log Collectors with status "STOPPED" as a zip file
     *
     * @return string Binary content of zip file
     */
    public function downloadAllLogCollectorContentZip()
    {
        $url = self::makeUrl(null, true);
        return $this->service->prepAndSend($url, array(200), "GET", null, true, "application/json", "application/zip");
    }

    /**
     * Update an existing Log Collector's settings,
     *  you cannot change "name" after a collectors has been created on the server.
     *  you cannot re-run a collector which has been stopped.
     *
     * @param \Jaspersoft\Dto\Diagnostic\LogCollectorSettings $collector
     * @throws \Jaspersoft\Exception\MissingValueException
     * @returns \Jaspersoft\Dto\Diagnostic\LogCollectorSettings
     */
    public function updateLogCollector(LogCollectorSettings $collector)
    {
        if (!is_null($collector->id())) {
            $url = self::makeUrl($collector->id());
        } else {
            throw new MissingValueException("LogCollectorSettings requires id to be set,
                first request the LogCollectorSettings using logCollectorStates method to allow server to set ID");
        }

        $response = $this->service->prepAndSend($url, array(200), "PUT", $collector->toJSON(), true);
        return LogCollectorSettings::createFromJSON(json_decode($response));
    }

    /**
     * Stop all running log collectors
     *
     * @return array New representation of existing Log Collectors following modification
     */
    public function stopAllLogCollectors()
    {
        $url = self::makeUrl();

        $body = json_encode(array("patch" =>
            array(array("field" => "status", "value" => "STOPPED"))));

        $response = $this->service->prepAndSend($url, array(200), "PATCH", $body, true);
        $responseObj = json_decode($response);
        if (!empty($responseObj)) {

            $result = array();
            foreach ($responseObj->CollectorSettingsList as $lcs) {
                $result[] = LogCollectorSettings::createFromJSON($lcs);
            }
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Delete all Log Collector folders
     *
     * @return bool True if delete was successful, false otherwise
     */
    public function deleteAllLogCollectors()
    {
        $url = self::makeUrl();
        return $this->service->prepAndSend($url, array(200), "DELETE");
    }

    /**
     * Delete a Log Collector by id
     *
     * @param $id
     * @return bool True if delete was successful, false otherwise
     */
    public function deleteLogCollector($id)
    {
        $url = self::makeUrl($id);
        return $this->service->prepAndSend($url, array(200), "DELETE");
    }



}