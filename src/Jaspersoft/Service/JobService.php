<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Job\Job;
use Jaspersoft\Dto\Job\JobState;
use Jaspersoft\Dto\Job\JobSummary;

class JobService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}
		
    private function make_url($params = null) {
        $url = $this->restUrl2 . '/jobs';
        if (!empty($params))
            $url .= '?' . Util::query_suffix($params);
        return $url;
    }
	
    /**
     * Search for scheduled jobs.
     *
     * @param null $reportUnitURI - URI of the report (optional)
     * @param null $owner - Search by user who created job
     * @param null $label - Search by job label title
     * @param null $example - Search by any field of Job description in JSON format (i.e: {"outputFormats" : ["RTF", "PDF" ]} )
     * @param null $startIndex - Start at this number (pagination)
     * @param null $rows - Number of rows in a block (pagination)
     * @param null $sortType - How to sort by column, must be any of the following:
     * NONE, SORTBY_JOBID, SORTBY_JOBNAME, SORTBY_REPORTURI, SORTBY_REPORTNAME, SORTBY_REPORTFOLDER,
     * SORTBY_OWNER, SORTBY_STATUS, SORTBY_LASTRUN, SORTBY_NEXTRUN
     * @param bool $ascending - Sorting direction, ascending if true, descending if false
     * @return array|NULL
     */
    public function searchJobs($reportUnitURI = null, $owner = null, $label = null, $example = null, $startIndex = null,
        $rows = null, $sortType = null, $ascending = null)
    {
        $result = array();
        $url = self::make_url(array(
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
     * Request a job object from server by JobID.
	 *
     * JobID can be found using getId() from an array of jobs returned by the getJobs function.
	 *
	 * @param int|string $id - the ID of the job you wish to know more about
	 * @return Job object
	 */
	public function getJob($id) {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}
	
	/**
	 * This function creates a new job on the server
	 * 
	 * @param Job $job object describing new job
	 * @return Job the server returned job with assigned ID
	 */
	public function createJob(Job $job)
	{
		$url = $this->restUrl2 . '/jobs';
		$data = $this->service->prepAndSend($url, array(201, 200), 'PUT', $job->toJSON(), true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}

	/**
	 * This function updates a new job on the server
	 * 
	 * @param Job $job object describing new data for the job
	 * @return Job the server returned job as it is now stored
	 */
	public function updateJob($job)
	{
		$url = $this->restUrl2 . '/jobs/' . $job->id;
		$data = $this->service->prepAndSend($url, array(201, 200), 'POST', $job->toJSON(), true, 'application/job+json', 'application/job+json');
        return Job::createFromJSON(json_decode($data));
	}
	
	/**
     * Delete a scheduled task.
	 *
     * This function will delete a job that is scheduled.
     * You must supply the Job's ID to this function to delete it.
	 *
	 * @param int|string $id - can be retrieved from JobSummary properties
     * @return string ID of deleted job
	 */
	public function deleteJob($id) {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->service->prepAndSend($url, array(200), 'DELETE', null, true);
        return $data;
	}

	/**
     * Get the State of a Job.
     *
	 * This function returns an array with state values
	 *
	 * @param int|string $id - can be retrieved using getId() on a JobSummary object
	 * @return \Jaspersoft\Dto\Job\JobState
	 */
	public function getJobState($id) {
		$url = $this->restUrl2 . '/jobs/' . $id . '/state';
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return JobState::createFromJSON(json_decode($data));
	}
	
	/**
     * Pause a job, all jobs, or multiple jobs.
	 *
	 * @param string|array|int|null $jobsToStop - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs.
	 * @return bool - based on success of function
	 */
	public function pauseJob($jobsToStop = null) {
		$url = $this->restUrl2 . '/jobs/pause';
        $body = json_encode(array("jobId" => (array) $jobsToStop));
		return $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
	}
	
	/**
     * Resume a job, all jobs, or multiple jobs.
	 *
	 * @param string|array|int|null $jobsToResume - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs
	 * @return bool - based on success of function
	 */
	public function resumeJob($jobsToResume = null) {
		$url = $this->restUrl2 . '/jobs/resume';
        $body = json_encode(array("jobId" => (array) $jobsToResume));
        return $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
	}
	
}

?>