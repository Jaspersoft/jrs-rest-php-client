<?php
namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Job\Job;
use Jaspersoft\Dto\Job\JobState;
use Jaspersoft\Dto\Job\JobSummary;

/**
 * Class JobService
 * @package Jaspersoft\Service
 */
class JobService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }
		
    private function makeUrl($params = null)
    {
        $url = $this->restUrl2 . '/jobs';
        if (!empty($params))
            $url .= '?' . Util::query_suffix($params);
        return $url;
    }
	
    /**
     * Search for scheduled jobs.
     *
     * @param string $reportUnitURI URI of the report (optional)
     * @param string $owner Search by user who created job
     * @param string $label Search by job label title
     * @param string $example Search by any field of Job description in JSON format (i.e: {"outputFormats" : ["RTF", "PDF" ]} )
     * @param int $startIndex Start at this number (pagination)
     * @param int $rows Number of rows in a block (pagination)
     * @param string $sortType How to sort by column, must be any of the following:
     *        NONE, SORTBY_JOBID, SORTBY_JOBNAME, SORTBY_REPORTURI, SORTBY_REPORTNAME, SORTBY_REPORTFOLDER,
     *        SORTBY_OWNER, SORTBY_STATUS, SORTBY_LASTRUN, SORTBY_NEXTRUN
     * @param boolean $ascending
     * @return array
     */
    public function searchJobs($reportUnitURI = null, $owner = null, $label = null, $example = null, $startIndex = null,
        $rows = null, $sortType = null, $ascending = null)
    {
        $result = array();
        $url = self::makeUrl(array(
            'reportUnitURI' => $reportUnitURI,
            'owner' => $owner,
            'label' => $label,
            'example' => $example,
            'startIndex' => $startIndex,
            'numberOfRows' => $rows,
            'sortType' => $sortType,
            'isAscending' => $ascending
        ));

        $resp = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (empty($resp))
            return $result;
        $jobs = json_decode($resp);
        foreach($jobs->jobsummary as $job) {
            $result[] = @new JobSummary(
                $job->id,
                $job->label,
                $job->reportUnitURI,
                $job->version,
                $job->owner,
                $job->state->value,
                $job->state->nextFireTime,
                $job->state->previousFireTime
            );
        }
        return $result;
    }
	
	/**
     * Get job descriptor
	 *
	 * @param int|string $id
	 * @return \Jaspersoft\Dto\Job\Job
	 */
	public function getJob($id)
    {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}
	
	/**
	 * Create a new job
	 * 
	 * @param \Jaspersoft\Dto\Job\Job $job object describing new job
	 * @return \Jaspersoft\Dto\Job\Job the server returned job with assigned ID
	 */
	public function createJob(Job $job)
	{
		$url = $this->restUrl2 . '/jobs';
		$data = $this->service->prepAndSend($url, array(201, 200), 'PUT', $job->toJSON(), true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}

	/**
	 * Update a job
	 * 
	 * @param \Jaspersoft\Dto\Job\Job $job object describing new data for the job
	 * @return \Jaspersoft\Dto\Job\Job the server returned job as it is now stored
	 */
	public function updateJob($job)
	{
		$url = $this->restUrl2 . '/jobs/' . $job->id;
		$data = $this->service->prepAndSend($url, array(201, 200), 'POST', $job->toJSON(), true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}
	
	/**
     * Delete a job
	 *
     * This function will delete a job that is scheduled.
     * You must supply the Job's ID to this function to delete it.
	 *
	 * @param int|string $id
     * @return string
	 */
	public function deleteJob($id)
    {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->service->prepAndSend($url, array(200), 'DELETE', null, true);
        return $data;
	}

	/**
     * Get the State of a Job
	 *
	 * @param int|string $id
	 * @return \Jaspersoft\Dto\Job\JobState
	 */
	public function getJobState($id)
    {
		$url = $this->restUrl2 . '/jobs/' . $id . '/state';
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return JobState::createFromJSON(json_decode($data));
	}
	
	/**
     * Pause a job, all jobs, or multiple jobs.
	 *
	 * @param string|array|int|null $jobsToStop Setting this value to null implies 'all jobs'
	 * @return boolean
	 */
	public function pauseJob($jobsToStop = null)
    {
		$url = $this->restUrl2 . '/jobs/pause';
        $body = json_encode(array("jobId" => (array) $jobsToStop));
		return $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
	}
	
	/**
     * Resume a job, all jobs, or multiple jobs.
     *
	 * @param string|array|int|null $jobsToResume Setting this value to null implies 'all jobs'
	 * @return boolean
	 */
	public function resumeJob($jobsToResume = null)
    {
		$url = $this->restUrl2 . '/jobs/resume';
        $body = json_encode(array("jobId" => (array) $jobsToResume));
        return $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
	}
	
}