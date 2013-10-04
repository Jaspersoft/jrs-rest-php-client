<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Permission\RepositoryPermission;

class PermissionService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}

    /**
     * Return an array of ReportPermission objects from the JSON data representing the
     * batch collection of one or more Permissions.
     *
     * @param $json_data string JSON data representing the result of a batch permission operation
     * @return array A set of RepositoryPermission items described by the JSON data
     */
    private function batchDataToArray($json_data)
    {
        $result = array();
        foreach (json_decode($json_data) as $perm) {
            $result[] = @new RepositoryPermission($perm->uri, $perm->recipient, $perm->mask);
        }
        return $result;
    }

    /**
     * Obtain the permissions of a resource on the server
     *
     * @param $uri string of resource you wish to obtain permissions
     * @param $effectivePermissions string shows all permissions who affect uri
     * @param $recipientType string Type type of permission (user or role)
     * @param $recipientId string the id of the recipient (requires recipientType)
     * @param $resolveAll string resolve for all matched recipients
     * @return array<RepositoryPermission> array of permission objects
     */
    public function searchRepositoryPermissions($uri, $effectivePermissions = null, $recipientType = null, $recipientId = null, $resolveAll = null) {
		$url = $this->restUrl2 . '/permissions' . $uri;
		$url .= '?' . Util::query_suffix(array(
								"effectivePermissions" => $effectivePermissions,
								"recipientType" => $recipientType,
								"recipientId" => $recipientId,
								"resolveAll" => $resolveAll));
		$data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (empty($data)) return false;
        return self::batchDataToArray($data);
	}


    /** Get a single permission
     *
     * @param $uri string URI of the resource within the repository
     * @param $recipientUri string The URI describing the recipient (user or role) you seek
     * @return \Jaspersoft\Dto\Permission\RepositoryPermission
     */
    public function getRepositoryPermission($uri, $recipientUri)
    {
        $url = $this->restUrl2 . '/permissions' . $uri;
        $url .= ";recipient=" . str_replace('/', '%2F', $recipientUri);
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        return RepositoryPermission::createFromJSON($data);
    }

    /**
     * Update a single RepositoryPermission.
     *
     * Note that only the mask of a RepositoryPermission can be changed using this method.
     *
     * @param $uri string Location of the repository item
     * @param $permission \Jaspersoft\Dto\Permission\RepositoryPermission The object representing the revised Permission
     * @return RepositoryPermission
     */
    public function updateRepositoryPermission($uri, $permission)
    {
        $url = $this->restUrl2 . '/permissions' . $uri;
        $url .= ";recipient=" . str_replace('/', '%2F', $permission->recipient);
        $data = $this->service->prepAndSend($url, array(200, 201), 'PUT', null, true);
        return RepositoryPermission::createFromJSON($data);
    }

	/**
	 * Update a set of permissions on the server.
	 *
	 * @param $uri string URI of the resource in the repository
	 * @param $permissions array an array of RepositoryPermission objects representing changes made
     * @return array Set of RepositoryPermissions
     */
	public function updateRepositoryPermissions($uri, $permissions) {
		$url = $this->restUrl2 . '/permissions' . $uri;
		$body = json_encode(array('permission' => $permissions));
		$permissions = $this->service->prepAndSend($url, array(201, 200), 'PUT', $body, true, 'application/collection+json', 'application/json');
        return self::batchDataToArray($permissions);
	}



    /**
     * Create new permissions on the server.
     *
     * @param $permissions array RepositoryPermission objects in an array completely defining new permissions
     * @return array Returns a set of RepositoryPermission items
     */
	public function createRepositoryPermissions($permissions) {
		$url = $this->restUrl2 . '/permissions';
		$body = json_encode(array('permission' => $permissions));
		$permissions = $this->service->prepAndSend($url, array(201, 200), 'POST', $body, true, 'application/collection+json', 'application/json');
        return self::batchDataToArray($permissions);
	}
	
	 /**
     * Remove an already existing permission.
     *
     * Simply provide the permission object you wish to delete. (use searchRepositoryPermissions to fetch existing permissions).
     *
     * @param RepositoryPermission $perm - object correlating to permission to be deleted.
     * @throws RESTRequestException
     * @return bool - based on success of function
     */
	public function deleteRepositoryPermission(RepositoryPermission $perm) {
		$url = $this->restUrl2 . '/permissions' . $perm->uri . ';recipient=' . str_replace('/', '%2F', $perm->recipient);
		$this->service->prepAndSend($url, array(200, 204), 'DELETE', null);
	}		

}
?>