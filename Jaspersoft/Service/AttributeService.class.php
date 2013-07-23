<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\Util;
use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Dto\Attribute\Attribute;
use Jaspersoft\Dto\User\User;

class AttributeService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}

	
	private function make_url($username, $tenantID = null, $attributeNames = null)
	{
        if (!empty($tenantID)) {
            $url = $this->restUrl2 . "/organizations/" . $tenantID . "/users/" . $username .
                "/attributes";
        } else {
            $url = $this->restUrl2 . "/users" . $username . "/attributes";
        }
        // Allow for parametrized attribute searches
        if (!empty($attributeNames)) {
            $url .= '?' . Util::query_suffix(array('name' => $attributeNames));
        }
        return $url;
    }
	
	/**
     * Combine two arrays of attribute objects. This function will replace attributes in $a with those in $b if the
     * same key is found in each array. Otherwise, they will be combined.
     *
     * @param $a
     * @param $b
     * @return array
     */
    protected static function combineAttributeArrays($a, $b)
	{
        $one = array();
        $two = array();
        $result = array();
        foreach ($a as $attr) {
            $one[$attr->name] = $attr->value;
        }
        foreach ($b as $attr) {
            $two[$attr->name] = $attr->value;
        }
        $combine = array_replace($one, $two);
        foreach ($combine as $k => $v) {
            $result[] = new Attribute($k, $v);
        }
        return $result;
    }

	/**
     * Retrieve attributes of a user.
     *
     * @param User $user - user object of the user you wish to retrieve data about
     * @param $attributeNames - An array of specific attribute names you seek
     * @return null|array<Attribute> - an array of attribute objects
     * @throws Exception - if HTTP fails
     */
    public function getAttributes(User $user, $attributeNames = null)
	{
        $result = array();
        $url = self::make_url($user->getUsername(), $user->getTenantId(), $attributeNames);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');

        if(!empty($data)) {
            $json = json_decode($data);
        } else {
            return null;
        }

        foreach($json->attribute as $element) {
            $tempAttribute = new Attribute(
                $element->name,
                $element->value);
            $result[] = $tempAttribute;
        }
        return $result;
    }
	
	/**
     * Replace all existing attributes for a user with those defined in the attributes array parameter
     *
     * @param User $user
     * @param $attributes - An array of attribute objects (must be array)
     */
    public function updateAttributes(User $user, $attributes)
	{
        $url = self::make_url($user->getUsername(), $user->getTenantId());
        $data = json_encode(array('attribute' => $attributes));
        $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, 'application/json', 'application/json');
    }

	/**
     * Create new attributes, or replace existing attributes.
     *
     * This function will add attributes to a user in addition to those already in place. If an attribute with the same
     * name already exists, it will be replaced by the new value supplied.
     * @param $user - User object to modify
     * @param $attributes - An array or single attribute object to add or update on a user
     */
    public function addAttributes(User $user, $attributes)
	{
        $url = self::make_url($user->getUsername(), $user->getTenantId());
        if (!is_array($attributes))
            $attributes = array($attributes);
        $existing_attributes = $this->getAttributes($user);
        if (is_array($existing_attributes) && ($existing_attributes) > 0)
            $attributes = $this::combineAttributeArrays($existing_attributes, $attributes);
        $data = json_encode(array('attribute' => $attributes));
        $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, false, 'application/json', 'application/json');
    }
	
	/**
	 * Remove all attributes, or specific attributes from a user.
	 * 
	 * @param $user User object to delete attributes from
	 * @param $attributes An array of attribute names that are to be removed
	 */
	public function deleteAttributes(User $user, $attributes = null)
	{
		$url = self::make_url($user->getUsername(), $user->getTenantId());
		if (!empty($attributes)) {
			$url .= '?' . JasperClient::query_suffix(array('name' => $attributes));
		}
		$this->service->prepAndSend($url, array(204, 200), 'DELETE', null, false, 'application/json', 'application/json');
	}

}