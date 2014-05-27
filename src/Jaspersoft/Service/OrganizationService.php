<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Organization\Organization;
use Jaspersoft\Tool\Util;
use Jaspersoft\Client\Client;

/**
 * Class OrganizationService
 * @package Jaspersoft\Service
 */
class OrganizationService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }
	
	private function makeUrl($organization = null, $params = null)
	{
        $url = $this->restUrl2 . '/organizations';
        if (!empty($organization)) {
            $url .= '/' . $organization;
            return $url;
        }
        if (!empty($params))
            $url .= '?' . Util::query_suffix($params);
        return $url;
    }
	
	/**
     * Search for organizations
     *
     * Unlike the searchUsers function, full Organization objects are returned with this function.
     * You will receive an array with zero or more elements which are Organization objects that can be manipulated
     * or used with other functions requiring Organization objects.
     *
     * @param string $query
     * @param string $rootTenantId
     * @param int $maxDepth
     * @param boolean $includeParents
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchOrganizations($query = null, $rootTenantId = null, $maxDepth = null, $includeParents = null,
                                       $limit = null, $offset = null)
	{
        $result = array();
        $url = self::makeUrl(null, array(
            'q' => $query,
            'rootTenantId' => $rootTenantId,
            'maxDepth' => $maxDepth,
            'includeParents' => $includeParents,
            'limit' => $limit,
            'offset' => $offset));
        $resp = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (empty($resp))
            return $result;
        $orgs = json_decode($resp);
        foreach ($orgs->organization as $org) {
            $result[] = @new Organization($org->alias,
                $org->id,
                $org->parentId,
                $org->tenantName,
                $org->theme,
                $org->tenantDesc,
                $org->tenantFolderUri,
                $org->tenantNote,
                $org->tenantUri);
        }
        return $result;
    }
	
    /**
     * Create a new organization
     *
     * @param \Jaspersoft\Dto\Organization\Organization $org
     * @param boolean $createDefaultUsers
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function createOrganization(Organization $org, $createDefaultUsers = true)
	{
        $url = self::makeUrl(null, array('createDefaultUsers' => $createDefaultUsers));
        $data = json_encode($org);
        $this->service->prepAndSend($url, array(201), 'POST', $data, false, 'application/json', 'application/json');
    }

	/**
     * Delete an organization
	 *
	 * @param \Jaspersoft\Dto\Organization\Organization $org
	 * @throws \Jaspersoft\Exception\RESTRequestException
	 */
	public function deleteOrganization(Organization $org)
	{
        $url = self::makeUrl($org->id);
		$this->service->prepAndSend($url, array(204), 'DELETE', null, false);
	}
	
    /**
     * Update an organization
     *
     * @param \Jaspersoft\Dto\Organization\Organization $org
     */
    public function updateOrganization(Organization $org)
	{
        $url = self::makeUrl($org->id);
        $data = json_encode($org);
        $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, false, 'application/json', 'application/json');
    }
	
	/**
	 * Get an organization by ID
	 *
	 * @param int|string id The ID of the organization
	 * @return \Jaspersoft\Dto\Organization\Organization
	 */
	public function getOrganization($id)
	{
		$url = self::makeUrl($id);
		$data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
		$org = json_decode($data);
		return @new Organization(
				$org->alias,
                $org->id,
                $org->parentId,
                $org->tenantName,
                $org->theme,
                $org->tenantDesc,
                $org->tenantFolderUri,
                $org->tenantNote,
                $org->tenantUri
			    );
	}
}
