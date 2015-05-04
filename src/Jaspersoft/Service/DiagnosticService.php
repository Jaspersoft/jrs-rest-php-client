<?php

namespace Jaspersoft\Service;


use Jaspersoft\Dto\Diagnostic\Collector;

/**
 * Class DiagnosticService
 *
 * @package Jaspersoft\Service
 */
class DiagnosticService extends JRSService
{

    private function makeUrl()
    {
        $result = $this->service_url . '/diagnostic';

    }

    /**
     * Create and start a diagnostic log collector
     *
     * @param Collector $collector
     */
    public function createCollector(Collector $collector)
    {

    }

    /**
     * Obtain metadata about all Log Collectors
     *
     */
    public function collectorStates()
    {

    }

    /**
     * Obtain metadata about a specific Log Collector
     *
     * @param $id
     */
    public function collectorState($id)
    {

    }

    /**
     * Read and download the content of a Log Collector as a zip file
     *
     * @param $id
     */
    public function collectorContentZip($id)
    {

    }

    /**
     * Make changes to a Log Collector's settings
     *
     * @param Collector $collector
     */
    public function updateCollector(Collector $collector)
    {

    }

    /**
     * Delete all Log Collector folders
     *
     */
    public function deleteAllCollectors()
    {

    }

    /**
     * Delete a Log Collector by id
     *
     * @param $id
     */
    public function deleteCollector($id)
    {

    }



}