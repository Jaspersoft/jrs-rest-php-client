<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Organization\Organization;
use Jaspersoft\Dto\Attribute\Attribute;
use Jaspersoft\Tool\Util;

/**
 * Class OrganizationService
 * @package Jaspersoft\Service
 */
class OrganizationService extends JRSService
{
	
	private function makeUrl($organization = null, $params = null)
	{
        $url = $this->service_url . '/organizations';
        if (!empty($organization)) {
            $url .= '/' . $organization;
            return $url;
        }
        if (!empty($params))
            $url .= '?' . Util::query_suffix($params);
        return $url;
    }

    private function makeAttributeUrl($tenantID, $attributeNames = null, $attrName = null)
    {
        $url = $this->service_url . "/organizations/" . $tenantID . "/attributes";
        // Allow for parametrized attribute searches
        if (!empty($attributeNames)) {
            $url .= '?' . Util::query_suffix(array('name' => $attributeNames));
        } else if (!empty($attrName)) {
            $url .= '/' . str_replace(' ', '%20', $attrName); // replace spaces with %20 url encoding
        }
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

    /**
     * Retrieve attributes of an organization.
     *
     * @param Organization $organization
     * @param array $attributeNames
     * @return null|array
     * @throws \Exception
     */
    public function getAttributes(Organization $organization, $attributeNames = null)
    {
        $result = array();
        $url = self::makeAttributeUrl($organization->id, $attributeNames);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true);
        $jsonObj = json_decode($data);
        if (!empty($jsonObj)) {
            $result = array();
            foreach ($jsonObj->attribute as $attr) {
                $result[] = Attribute::createFromJSON($attr);
            }
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Create a non-existent attribute, or update an existing attribute
     *
     * @param Organization $organization
     * @param Attribute $attribute
     * @return bool|null
     */
    public function addOrUpdateAttribute(Organization $organization, $attribute)
    {
        $url = self::makeAttributeUrl($organization->id, null, $attribute->name);
        $data = json_encode($attribute);
        $response = $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, true);

        return Attribute::createFromJSON(json_decode($response));
    }

    /**
     * Replace all existing attributes with the provided set
     *
     * @param Organization $organization
     * @param array $attributes
     * @return array The server representation of the replaced attributes
     */
    public function replaceAttributes(Organization $organization, array $attributes)
    {
        $url = self::makeAttributeUrl($organization->id);
        $data = json_encode(array('attribute' => $attributes));
        $response = $this->service->prepAndSend($url, array(200), 'PUT', $data, true);
        $response = json_decode($response);

        $result = array();
        foreach ($response->attribute as $attr) {
            $result[] = Attribute::createFromJSON($attr);
        }
        return $result;
    }

    /**
     * Remove all attributes, or specific attributes from an organization.
     *
     * @param Organization $organization
     * @param array $attributes
     * @return bool
     */
    public function deleteAttributes(Organization $organization, $attributes = null)
    {
        $url = self::makeAttributeUrl($organization->id);
        if (!empty($attributes)) {
            $url .= '?' . Util::query_suffix(array('name' => $attributes));
        }
        return $this->service->prepAndSend($url, array(204), 'DELETE', null, false);
    }

}
