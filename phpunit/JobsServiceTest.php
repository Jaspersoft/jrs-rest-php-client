<?php
use Jasper\JasperClient;
use Jasper\Job;
use Jasper\JobSummary;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperJobTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient $jc */
	protected $jc;


	public function setUp() {
		$bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');

		$this->jc = new JasperClient(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['admin_username'],
				$bootstrap['admin_password'],
				$bootstrap['base_url'],
				$bootstrap['admin_org']
		);
	}

	public function tearDown() {

	}

    /**
     * Checks putJob() functionality - whether it actually creates a Job on server.
     */
    public function testPutJob_createsNewJob() {
		$folderForJob = JasperTestUtils::createFolder();
		$job = JasperTestUtils::createJob($folderForJob);

		$jobsBeforePut = $this->jc->getJobs();
		$this->jc->putResource('', $folderForJob);
		$newJobId = $this->jc->putJob($job);

		$jobsAfterPut = $this->jc->getJobs();

		$this->jc->deleteJob($newJobId);
		$this->jc->deleteResource($folderForJob->getUriString());

		$this->assertEquals((count($jobsBeforePut) + 1), count($jobsAfterPut));
	}

    /**
     * Checks postJob() functionality - whether it modifies a previously created Job.
     */
    public function testPostJob_updatesJob() {
		$folder = JasperTestUtils::createFolder();
		$job = JasperTestUtils::createJob($folder);

		$this->jc->putResource('', $folder);
		$jobServerId = $this->jc->putJob($job);

		$job->id = $jobServerId;

		$unique = md5(microtime());

		$job->description = $unique;
		$job->version = '0';

		$this->jc->postJob($job);

		$jobObjFromServer = $this->jc->getJob($jobServerId);

		$this->jc->deleteJob($jobServerId);
		$this->jc->deleteResource($folder->getUriString());

		$this->assertEquals($unique, $jobObjFromServer->description);
	}

    /**
     * Checks getJobs() functionality - whether it gets all Jobs present on the server.
     */
    public function testJobSummary_allCurrentJobs() {
        $jobsBefore = $this->jc->getJobs();

        $folder = JasperTestUtils::createFolder();
        $this->jc->putResource('', $folder);

        $jobId[] = $this->jc->putJob(JasperTestUtils::createJob($folder));
        $jobId[] = $this->jc->putJob(JasperTestUtils::createJob($folder));

        $jobsAfter = $this->jc->getJobs();

        foreach ($jobId as &$id) {
            $this->jc->deleteJob($id);
        }

	$this->jc->deleteResource($folder->getUriString());

        $this->assertEquals(count($jobsBefore)+2, count($jobsAfter));
    }

   /**
    * Checks getJobs() returns empty array when no jobs are set.
    */
  public function testGetJobs_returnsEmptyWithNoJobs() {
      $folder = JasperTestUtils::createFolder();
      $this->jc->putResource('', $folder);

      $jobs = $this->jc->getJobs();

      $this->jc->deleteResource($folder->getUriString());
     
      $this->assertEquals(0, count($jobs));	

  }

}

?>
