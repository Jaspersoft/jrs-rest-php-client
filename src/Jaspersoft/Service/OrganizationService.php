<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Organization\Organization;
use Jaspersoft\Tool\Util;
use Jaspersoft\Client\Client;

class OrganizationService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }
	
	private function make_url($organization = null, $params = null)
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
     * Use this function to search for organizations.
     *
     * Unlike the searchUsers function, full Organization objects are returned with this function.
     * You will receive an array with zero or more elements which are Organization objects that can be manipulated
     * or used with other functions requiring Organization objects.
     *
     * @param null $query
     * @param null $rootTenantId
     * @param null $maxDepth
     * @param null $includeParents
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function searchOrganizations($query = null, $rootTenantId = null, $maxDepth = null, $includeParents = null,
                                       $limit = null, $offset = null)
	{
        $result = array();
        $url = self::make_url(null, array(
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
     * This function creates a new organization. If you do not wish for default users to be created
     * supply false as the second parameter.
     *
     * @param Organization $org
     * @param bool $createDefaultUsers
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function createOrganization(Organization $org, $createDefaultUsers = true)
	{
        $url = self::make_url(null, array('createDefaultUsers' => $createDefaultUsers));
        $data = json_encode($org);
        $this->service->prepAndSend($url, array(201), 'POST', $data, false, 'application/json', 'application/json');
    }

	/**
     * Delete an organization.
	 *
	 * @param Organization $org - organization object
	 * @throws \Jaspersoft\Exception\RESTRequestException
	 */
	public function deleteOrganization(Organization $org)
	{
        $url = self::make_url($org->getId());
		$this->service->prepAndSend($url, array(204), 'DELETE', null, false);
	}
	
    /**
     * This function updates an existing organization. Supply an organization object with the expected changes.
     *
     * @param Organization $org
     */
    public function updateOrganization(Organization $org)
	{
        $url = self::make_url($org->getId());
        $data = json_encode($org);
        $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, false, 'application/json', 'application/json');
    }
	
	/**
	 * This function requests the single entity of one organization when supplied with the ID
	 *
	 * @param string id The ID of the organization
	 * @return Organization
	 */
	public function getOrganization($id)
	{
		$url = self::make_url($id);
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
