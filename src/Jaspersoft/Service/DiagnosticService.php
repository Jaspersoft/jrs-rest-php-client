<?php

namespace Jaspersoft\Service;


use Jaspersoft\Dto\Diagnostic\LogCollectorSettings;

/**
 * Class DiagnosticService
 *
 * @package Jaspersoft\Service
 */
class DiagnosticService extends JRSService
{

    private function makeUrl()
    {
        // For now, only Log Collectors are a diagnostic tool
        // so we can assume all URLs will contain /collectors
        $result = $this->service_url . '/diagnostic/collectors';

        return $result;
    }

    /**
     * Create and start a diagnostic log collector
     *
     * @param LogCollectorSettings $collector
     * @return LogCollectorSettings Representation of created log collector
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
     */
    public function logCollectorStates()
    {

    }

    /**
     * Obtain metadata about a specific Log Collector
     *
     * @param $id
     */
    public function logCollectorState($id)
    {

    }

    /**
     * Download the content of a Log Collector as a zip file
     *
     * @param $id
     * @return string Binary content of zip file
     */
    public function downloadLogCollectorContentZip($id)
    {

    }

    /**
     * Download the content of all Log Collectors with status "STOP" as a zip file
     *
     * @return string Binary content of zip file
     */
    public function downloadAllLogCollectorContentZip()
    {

    }

    /**
     * Make changes to a Log Collector's settings
     *
     * @param LogCollectorSettings $collector
     */
    public function updateLogCollector(LogCollectorSettings $collector)
    {

    }

    /**
     * Delete all Log Collector folders
     *
     */
    public function deleteAllLogCollectors()
    {

    }

    /**
     * Delete a Log Collector by id
     *
     * @param $id
     */
    public function deleteLogCollector($id)
    {

    }



}