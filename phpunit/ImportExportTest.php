<?php

use Jasper\JasperClient;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class ImportExportTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
    protected $jc;
    protected $newUser;
    protected $import_file;

    public function setUp() {
        $bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');
        $this->jc = new JasperClient(
            $bootstrap['hostname'],
            $bootstrap['port'],
            $bootstrap['super_username'],
            $bootstrap['super_password'],
            $bootstrap['base_url']
        );
        $this->import_file = file_get_contents( dirname(__FILE__) . '/resources/blank-slate-export.zip');
    }

    public function tearDown() {

    }

    /** This test is VERY time consuming (>1min usually) */
    public function test_exportService() {
        $task = new \Jasper\ExportTask('everything');
        $metadata = $this->jc->startExportTask($task);
        $state = $this->jc->getExportState($metadata['id']);
        $running = true;

        while ($running) {
            $state = $this->jc->getExportState($metadata['id']);
            if ($state['phase'] == "inprogress")
                sleep(5);
            else
                $running = false;
        }

        $this->assertEquals('finished', $state['phase']);
        // No assertions are made, but an exception will be thrown if fetchExport fails
        $data = $this->jc->fetchExport($metadata['id']);
        unset($data);
    }

    /** This test is VERY time consuming (>1min usually) */
    public function test_importService() {
        $task = new \Jasper\ImportTask();
        $task->update = true;
        $metadata = $this->jc->startImportTask($task, $this->import_file);
        $state = $this->jc->getImportState($metadata['id']);
        $running = true;

        while ($running) {
            $state = $this->jc->getImportState($metadata['id']);
            if ($state['phase'] == "inprogress")
                sleep(5);
            else
                $running = false;
        }
        $this->assertEquals('finished', $state['phase']);
        unset($this->import_file);

    }


}
