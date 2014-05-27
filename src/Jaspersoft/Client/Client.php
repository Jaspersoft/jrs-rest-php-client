<?php
namespace Jaspersoft\Client;

use Jaspersoft\Service as service;
use Jaspersoft\Tool\RESTRequest;

define("BASE_REST2_URL", "/rest_v2");

/**
 * Class Client
 *
 * Defines the JasperReports server information, and provides services to be used for various tasks.
 *
 * @package Jaspersoft\Client
 */
class Client
{
    private $restReq;
    private $restUrl2;
	protected $hostname;
	protected $username;
	protected $password;
	protected $orgId;
    protected $repositoryService;
    protected $userService;
    protected $organizationService;
    protected $roleService;
    protected $jobService;
    protected $permissionService;
    protected $optionsService;
    protected $reportService;
    protected $importExportService;
    protected $queryService;

	public function __construct($serverUrl, $username, $password, $orgId = null)
	{
		$this->serverUrl = $serverUrl;
		$this->username = $username;
		$this->password = $password;
		$this->orgId = $orgId;

		$this->restReq = new RESTRequest();
		if (!empty($this->orgId)) {
			$this->restReq->setUsername($this->username .'|'. $this->orgId);
		} else {
			$this->restReq->setUsername($this->username);
		}
		$this->restReq->setPassword($this->password);
		$this->restUrl2 = $this->serverUrl . BASE_REST2_URL;
	}

    public function repositoryService()
    {
        if (!isset($this->repositoryService)) {
            $this->repositoryService = new service\RepositoryService($this);
        }
        return $this->repositoryService;
    }

	public function userService()
    {
        if (!isset($this->userService)) {
            $this->userService = new service\UserService($this);
        }
        return $this->userService;
	}
	
	public function organizationService()
    {
        if (!isset($this->organizationService)) {
            $this->organizationService = new service\OrganizationService($this);
        }
        return $this->organizationService;
	}
	
	public function roleService()
    {
        if (!isset($this->roleService)) {
            $this->roleService = new service\RoleService($this);
        }
        return $this->roleService;
	}
	
	public function jobService()
    {
        if (!isset($this->jobService)) {
            $this->jobService = new service\JobService($this);
        }
        return $this->jobService;
	}
	
	public function permissionService()
    {
        if (!isset($this->permissionService)) {
            $this->permissionService = new service\PermissionService($this);
        }
        return $this->permissionService;
	}
	
	public function optionsService()
    {
        if (!isset($this->optionsService)) {
            $this->optionsService = new service\OptionsService($this);
        }
        return $this->optionsService;
	}
	
	public function reportService()
    {
        if (!isset($this->reportService)) {
            $this->reportService = new service\ReportService($this);
        }
        return $this->reportService;
	}
	
	public function importExportService()
    {
        if (!isset($this->importExportService)) {
            $this->importExportService = new service\ImportExportService($this);
        }
        return $this->importExportService;
    }
	
	public function queryService()
    {
        if (!isset($this->queryService)) {
            $this->queryService = new service\QueryService($this);
        }
        return $this->queryService;
    }

    /** setRequestTimeout
     *
     * Set the amount of time cURL is permitted to wait for a response to a request before timing out.
     *
     * @param $seconds int Time in seconds
     */
    public function setRequestTimeout($seconds)
    {
        $this->restReq->defineTimeout($seconds);
    }
	
    /** This function returns information about the server in an associative array.
     * Information provided is:
     *
     * - Date/Time Formatting Patterns
     * - Edition
     * - Version
     * - Build
     * - Features
     * - License type and expiration
     *
     * @return array
     */
    public function serverInfo()
    {
        $url = $this->restUrl2 . '/serverInfo';
        $data = $this->restReq->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($data, true);
    }

    /**
     * Provides the constructed RESTv2 URL for the defined JasperReports Server
     * @return string
     */
    public function getURL() { return $this->restUrl2; }

    /**
     * Provides the RESTRequest object to be reused by the services that require it
     * @return RESTRequest
     */
    public function getService() { return $this->restReq; }

}