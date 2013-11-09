<?php
namespace Jaspersoft\Client;

use Jaspersoft\Service as service;
use Jaspersoft\Tool\RESTRequest;

define("BASE_REST2_URL", "/rest_v2");

class Client
{
	protected $hostname;
	protected $port;
	protected $username;
	protected $password;
	protected $orgId;
	protected $baseUrl;
	private $restReq;
	private $restUrl2;
	
	public function __construct($hostname = 'localhost', $port = '8080', $username = null, $password = null, $baseUrl = "/jasperserver-pro", $orgId = null)
	{
		$this->hostname = $hostname;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->baseUrl = $baseUrl;
		$this->orgId = $orgId;

		$this->restReq = new RESTRequest();
		if (!empty($this->orgId)) {
			$this->restReq->setUsername($this->username .'|'. $this->orgId);
		} else {
			$this->restReq->setUsername($this->username);
		}
		$this->restReq->setPassword($this->password);
		$this->restUrl2 = "http://" . $this->hostname . ':' . $this->port . $this->baseUrl . BASE_REST2_URL;
	}

    public function repositoryService() {
        return new service\RepositoryService($this->restReq, $this->restUrl2);
    }
	
	public function attributeService() {
		return new service\AttributeService($this->restReq, $this->restUrl2);
	}

	public function userService() {
		return new service\UserService($this->restReq, $this->restUrl2);
	}
	
	public function organizationService() {
		return new service\OrganizationService($this->restReq, $this->restUrl2);
	}
	
	public function roleService() {
		return new service\RoleService($this->restReq, $this->restUrl2);
	}
	
	public function jobService() {
		return new service\JobService($this->restReq, $this->restUrl2);
	}
	
	public function permissionService() {
		return new service\PermissionService($this->restReq, $this->restUrl2);
	}
	
	public function optionsService() {
		return new service\OptionsService($this->restReq, $this->restUrl2);
	}
	
	public function reportService() {
		return new service\ReportService($this->restReq, $this->restUrl2);
	}
	
	public function importExportService() {
		return new service\ImportExportService($this->restReq, $this->restUrl2);
	}
	
	public function queryService() {
		return new service\QueryService($this->restReq, $this->restUrl2);
	}

    /** setRequestTimeout
     *
     * Set the amount of time cURL is permitted to wait for a response to a request before timing out.
     *
     * @param $seconds int Time in seconds
     */
    public function setRequestTimeout($seconds) {
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
    public function serverInfo() {
        $url = $this->restUrl2 . '/serverInfo';
        $data = $this->restReq->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($data, true);
    }


}