<?php
require_once "BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\ImportExport\ImportTask;
use Jaspersoft\Dto\ImportExport\ExportTask;

class ImportExportServiceTest extends BaseTest {

    protected $jc;
    protected $newUser;
    protected $import_file;

    public function setUp() {
		parent::setUp();
		$this->ies = $this->jc->importExportService();
        $this->import_file = file_get_contents( dirname(__FILE__) . '/resources/blank-slate-export.zip');    }

    public function tearDown() {

    }

    /** This test is VERY time consuming (>1min usually) */
    public function test_exportService() {
        $task = new ExportTask('everything');
        $metadata = $this->ies->startExportTask($task);
        $state = $this->ies->getExportState($metadata['id']);
        $running = true;

        while ($running) {
            $state = $this->ies->getExportState($metadata['id']);
            if ($state['phase'] == "inprogress")
                sleep(5);
            else
                $running = false;
        }

        $this->assertEquals('finished', $state['phase']);
        // No assertions are made, but an exception will be thrown if fetchExport fails
        $data = $this->ies->fetchExport($metadata['id']);
        unset($data);
    }

    /** This test is VERY time consuming (>1min usually) */
    public function test_importService() {
        $task = new ImportTask();
        $task->update = true;
        $metadata = $this->ies->startImportTask($task, $this->import_file);
        $state = $this->ies->getImportState($metadata['id']);
        $running = true;

        while ($running) {
            $state = $this->ies->getImportState($metadata['id']);
            if ($state['phase'] == "inprogress")
                sleep(5);
            else
                $running = false;
        }
        $this->assertEquals('finished', $state['phase']);
        unset($this->import_file);

    }


}
