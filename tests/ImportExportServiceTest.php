<?php
require_once "BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\ImportExport\ImportTask;
use Jaspersoft\Dto\ImportExport\ExportTask;

class ImportExportServiceTest extends BaseTest
{
    protected $jc;
	protected $jcSuper;
    protected $newUser;
    protected $import_file;

    public function setUp()
	{
		parent::setUp();
		parent::createSuperClient();
		$this->ies = $this->jcSuper->importExportService();
        $this->import_file = file_get_contents( dirname(__FILE__) . '/resources/jasperadmin_import.zip');    
	}

    public function tearDown()
	{
		parent::tearDown();
    }

    public function test_exportService()
	{
        $task = new ExportTask();
		$task->users[] = "jasperadmin|organization_1";
        $metadata = $this->ies->startExportTask($task);
        $state = $this->ies->getExportState($metadata->id);
		
        $running = true;
        while ($running) {
            $state = $this->ies->getExportState($metadata->id);
            if ($state->phase == "inprogress")
                sleep(5);
            else
                $running = false;
        }

        $this->assertEquals("Jaspersoft\\Dto\\ImportExport\\TaskState", get_class($state));
        $this->assertEquals('finished', $state->phase);
        $data = $this->ies->fetchExport($metadata->id);
		$this->assertTrue(strlen($data) > 100);
        unset($data);
    }

    public function test_importService()
	{
        $task = new ImportTask();
        $task->update = true;
        $metadata = $this->ies->startImportTask($task, $this->import_file);
        $state = $this->ies->getImportState($metadata->id);

        if ($state == "finished")
            $running = false;
        else
            $running = true;

        while ($running) {
            $state = $this->ies->getImportState($metadata->id);
            if ($state->phase == "inprogress")
                sleep(5);
            else
                $running = false;
        }
        $this->assertEquals("Jaspersoft\\Dto\\ImportExport\\TaskState", get_class($state));
        $this->assertEquals('finished', $state->phase);
        unset($this->import_file);
    }

}
