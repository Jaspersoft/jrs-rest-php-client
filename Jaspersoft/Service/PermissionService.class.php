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
     * Obtain the permissions of a resource on the server
     *
     * @param $uri URI of resource you wish to obtain permissions
     * @param $effectivePermissions shows all permissions who affect uri
     * @param $recipientType	Type type of permission (user or role)
     * @param $recipientId the id of the recipient (requires recipientType)
     * @param $resolveAll resolve for all matched recipients
     * @return array<RepositoryPermission> array of permission objects
     */
    public function searchRepositoryPermissions($uri, $effectivePermissions = null, $recipientType = null, $recipientId = null, $resolveAll = null) {
		$result = array();
		$url = $this->restUrl2 . '/permissions' . $uri;
		$url .= '?' . JasperClient::query_suffix(array(
								"effectivePermissions" => $effectivePermissions,
								"recipientType" => $recipientType,
								"recipientId" => $recipientId,
								"resolveAll" => $resolveAll));
		$data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
		$permissions = json_decode($data);
		if (empty($permissions)) {
			return $result;
		}
		foreach ($permissions->permission as $p) {
			$result[] = @new RepositoryPermission(
							$p->uri,
							$p->recipient,
							$p->mask);
		}
		return $result;
	}

	/**
	 * Update a set of permissions on the server.
	 *
	 * @param $uri URI of the resource in the repository
	 * @param $permissions an array of RepositoryPermission objects representing changes made
	 */
	public function updateRepositoryPermissions($uri, $permissions) {
		$url = $this->restUrl2 . '/permissions' . $uri;
		$body = json_encode(array('permission' => $permissions));
		$this->service->prepAndSend($url, array(201, 200), 'PUT', $body, true, 'application/collection+json', 'application/json');
	}
	
	/**
	 * Create new permissions on the server.
	 *
	 * @param $permissions an array of RepositoryPermission objects completely defining new permissions
	 */
	public function createRepositoryPermissions($permissions) {
		$url = $this->restUrl2 . '/permissions';
		$body = json_encode(array('permission' => $permissions));
		$this->service->prepAndSend($url, array(201, 200), 'POST', $body, true, 'application/collection+json', 'application/json');
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