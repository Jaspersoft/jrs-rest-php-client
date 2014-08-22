<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Job\Calendar\BaseCalendar;
use Jaspersoft\Exception\RESTRequestException;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Job\Job;
use Jaspersoft\Dto\Job\JobState;
use Jaspersoft\Dto\Job\JobSummary;

/**
 * Class JobService
 * @package Jaspersoft\Service
 */
class JobService extends JRSService
{
    const CALENDAR_NAMESPACE = "Jaspersoft\\Dto\\Job\\Calendar";
		
    private function makeUrl($params = null)
    {
        $url = $this->service_url . '/jobs';
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
		$url = $this->service_url . '/jobs/' . $id;
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
		$url = $this->service_url . '/jobs';
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
		$url = $this->service_url . '/jobs/' . $job->id;
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
		$url = $this->service_url . '/jobs/' . $id;
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
		$url = $this->service_url . '/jobs/' . $id . '/state';
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
		$url = $this->service_url . '/jobs/pause';
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
		$url = $this->service_url . '/jobs/resume';
        $body = json_encode(array("jobId" => (array) $jobsToResume));
        return $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
	}

    /**
     * Obtain a listing of calendar names, optionally filtered by calendar type
     *
     * Possible calendarType values:
     *  "annual", "base", "cron", "daily", "holiday", "monthly", "weekly"
     *
     * @param string $calendarType Type of calendar to filter by
     * @return array Set of defined calendar names
     */
    public function getCalendarNames($calendarType = null)
    {
        $url = $this->service_url . '/jobs/calendars';
        $url .= (!empty($calendarType)) ? Util::query_suffix(array("calendarType" => $calendarType)) : null;

        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        if (empty($response)) return null;
        $calendars = json_decode($response);
        return $calendars->calendarName;
    }

    /**
     * Retrieve a calendar and its properties
     *
     * @param string $calendarName Name of the calendar to obtain details of
     * @return BaseCalendar
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function getCalendar($calendarName)
    {
        // rawurlencode will convert spaces to %20 as required by server
        $url = $this->service_url . '/jobs/calendars/' . rawurlencode($calendarName);
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        $calendarData = json_decode($response);
        if (empty($calendarData->calendarType)) {
            throw new RESTRequestException("JobService: Data format not expected.");
        }
        $className = JobService::CALENDAR_NAMESPACE . '\\'. ucfirst($calendarData->calendarType) . "Calendar";

        if (class_exists($className)) {
            return $className::createFromJSON($calendarData);
        } else {
            throw new RESTRequestException("JobService: Unrecognized calendar type returned by server");
        }
    }

    /**
     * Create or Update a calendar
     *
     * @param BaseCalendar $calendar A DTO representing the new or altered calendar
     * @param string $calendarName Name of the calendar to create or update
     * @param bool $replace Should an existing calendar of the same name be overwritten?
     * @param bool $updateTriggers Should an existing jobs using this calendar adhere to the changes made?
     */
    public function createOrUpdateCalendar(BaseCalendar $calendar, $calendarName, $replace = false, $updateTriggers = false)
    {
        $url = $this->service_url . '/jobs/calendars/' . rawurlencode($calendarName) . '?' .
            Util::query_suffix(array("replace" => $replace, "updateTriggers" => $updateTriggers));
        $body = $calendar->toJSON();
        $this->service->prepAndSend($url, array(200), 'PUT', $body, false);
    }

    /**
     * Delete a calendar by its name
     *
     * @param string $calendarName
     */
    public function deleteCalendar($calendarName) {
        $url = $this->service_url . '/jobs/calendars/' . rawurlencode($calendarName);
        $this->service->prepAndSend($url, array(204), 'DELETE');
    }

}