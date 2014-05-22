<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\Util;
use Jaspersoft\Client\Client;
use Jaspersoft\Dto\Attribute\Attribute;
use Jaspersoft\Dto\User\User;

class AttributeService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }

	
	private function make_url($username, $tenantID = null, $attributeNames = null, $attrName = null)
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
        } else if (!empty($attrName)) {
            $url .= '/' . str_replace(' ', '%20', $attrName); // replace spaces with %20 url encoding
        }
        return $url;
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
        $url = self::make_url($user->username, $user->tenantId, $attributeNames);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');

        if(!empty($data)) {
            $json = json_decode($data);
        } else {
            return $result;
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
     * Change an existing attribute, or create a new attribute without removing existing attributes. This will overwrite
     * any existing attribute data that matches the provided attribute.
     *
     * @param \Jaspersoft\Dto\User\User $user
     * @param \Jaspersoft\Dto\Attribute\Attribute $attribute
     * @return bool|null
     */
    public function setAttribute(User $user, $attribute)
	{
        $url = self::make_url($user->username, $user->tenantId, null, $attribute->name);
        $data = json_encode($attribute);
        return $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, false,
            'application/json', 'application/json');
    }

    /**
     * Replace all existing attributes for a user with those defined in the attributes array parameter
     *
     * @param User $user
     * @param $attributes - An array of attribute objects (must be array)
     */
    public function updateAttributes(User $user, $attributes)
    {
        $url = self::make_url($user->username, $user->tenantId);
        $data = json_encode(array('attribute' => $attributes));
        $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, 'application/json', 'application/json');
    }

	/**
	 * Remove all attributes, or specific attributes from a user.
	 * 
	 * @param $user \Jaspersoft\Dto\User\User object to delete attributes from
	 * @param $attributes array of attribute names that are to be removed
	 */
	public function deleteAttributes(User $user, $attributes = null)
	{
		$url = self::make_url($user->username, $user->tenantId);
		if (!empty($attributes)) {
			$url .= '?' . Util::query_suffix(array('name' => $attributes));
		}
		$this->service->prepAndSend($url, array(204), 'DELETE', null, false,
             'application/json', 'application/json');
	}

}