<?php
require_once "BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\Resource\Folder;
use Jaspersoft\Dto\Job\Job;
use Jaspersoft\Dto\Job\JobSummary;


class JobServiceTest extends BaseTest {

	protected $jc;
	protected $js;
	protected $rs;
	protected $testFolder;
	protected $testJob;

	/** Coverage: createJob, createResource **/
	public function setUp()
	{
		parent::setUp();

		$this->js = $this->jc->jobService();
		$this->rs = $this->jc->repositoryService();
		
		$this->testFolder = u::createFolder();
		$this->testJob = u::createJob($this->testFolder);		

		$this->rs->createResource($this->testFolder, "/");
		// Update local job object with server's response
		$this->testJob = $this->js->createJob($this->testJob);
	}
	
	/** Coverage: deleteJob, deleteResource **/
	public function tearDown()
	{
		parent::tearDown();
		$this->js->deleteJob($this->testJob->id);
		$this->rs->deleteResource($this->testFolder->uri);
	}

	/** Coverage: searchJobs **/
    public function testPutJob_createsNewJob()
	{
		$search = $this->js->searchJobs($this->testJob->source->reportUnitURI);
		$this->assertTrue(sizeof($search) > 0);
		$this->assertEquals($search[0]->label, $this->testJob->label);
	}

	/** Coverage: updateJob **/
	public function testUpdateJob_changesJob()
	{
		$this->testJob->label = "UPDATED_TO_TEST";
		$this->js->updateJob($this->testJob);
		$search = $this->js->searchJobs($this->testJob->source->reportUnitURI);
		$this->assertEquals($search[0]->label, "UPDATED_TO_TEST");
	}
	
	/** Coverage: getJob, getJobState **/
	public function testJobState()
	{
		$jobState = $this->js->getJobState($this->testJob->id);
		$this->assertTrue(!empty($jobState['value']));
	}
	
	/** Coverage: pauseJob, getJobState **/
	public function testPauseJob()
	{
		$this->js->pauseJob($this->testJob->id);
		$jobState = $this->js->getJobState($this->testJob->id);
		$this->assertEquals($jobState['value'], "PAUSED");
	}
	
	/** Coverage: pauseJob, getJobState, resumeJob **/
	public function testResumeJob()
	{
		self::testPauseJob();
		$this->js->resumeJob($this->testJob->id);
		$jobState = $this->js->getJobState($this->testJob->id);
		$this->assertEquals($jobState['value'], "NORMAL");
	}
	

}

?>
