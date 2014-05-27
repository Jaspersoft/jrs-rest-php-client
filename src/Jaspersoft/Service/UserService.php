<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Role\Role;
use Jaspersoft\Dto\User\UserLookup;
use Jaspersoft\Tool\Util;
use Jaspersoft\Client\Client;
use Jaspersoft\Dto\Attribute\Attribute;

/**
 * Class UserService
 * @package Jaspersoft\Service
 */
class UserService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(Client &$client)
	{
		$this->service = $client->getService();
		$this->restUrl2 = $client->getURL();
	}
	
	private function makeUserUrl($organization, $username = null, $params = null)
	{
        if(!empty($organization)) {
            $url = $this->restUrl2 . "/organizations/" . $organization . "/users";
        } else {
            $url = $this->restUrl2 . "/users";
        }
        if (!empty($username)) {
            $url .= '/' . $username;
            // Return early because no params can be set with single-user operations
            return $url;
        }
        if (!empty($params)) {
            $url .= '?' . Util::query_suffix($params);
        }
        return $url;
    }

    private function makeAttributeUrl($username, $tenantID = null, $attributeNames = null, $attrName = null)
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
     * Search for users based on the searchTerm provided.
     *
     * An array of zero or more UserLookup objects will be returned. These can then be passed one by one to
     * the getUserByLookup function to return the User Object of the user.
     *
     * If defining requiredRoles that exist in multiple organizations, you must suffix the ROLE name with
     * |organization_id (i.e: ROLE_USER|organization_1)
     *
     * @param string $searchTerm A query to filter results by
     * @param string $organization
     * @param array $requiredRoles
     * @param boolean $hasAllRequiredRoles
     * @param boolean $includeSubOrgs
     * @param int $limit A number to limit results by (pagination controls)
     * @param int $offset A number to offset the results by (pagination controls)
     * @return array
     */
    public function searchUsers($searchTerm = null, $organization = null,
                                $requiredRoles = null, $hasAllRequiredRoles = null, $includeSubOrgs = true, $limit = 0, $offset = 0)
    {
        $result = array();
        $url = self::makeUserUrl($organization, null,
            array('q' => $searchTerm,
                  'requiredRole' => $requiredRoles,
                  'hasAllRequiredRoles' => $hasAllRequiredRoles,
                  'includeSubOrgs' => $includeSubOrgs,
                  'limit' => $limit,
                  'offset' => $offset));
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (!empty($data)) {
            $users = json_decode($data);
            foreach ($users->user as $user) {
                $result[] = @new UserLookup(
                    $user->username,
                    $user->fullName,
                    $user->externallyDefined,
                    $user->tenantId
                );
            }
        }
        return $result;
    }

	/**
     * Return the user object represented by the provided UserLookup object
     *
     * @param \Jaspersoft\Dto\User\UserLookup $userLookup
     * @return \Jaspersoft\Dto\User\User
     */
    public function getUserByLookup(UserLookup $userLookup)
    {
        return $this->getUser($userLookup->username, $userLookup->tenantId);
    }

    /**
     * Request the User object for $username within $organization
     *
     * @param string $username
     * @param string $organization
     * @return \Jaspersoft\Dto\User\User
     */
    public function getUser($username, $organization = null)
	{
        $url = self::makeUserUrl($organization, $username);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        $userData = json_decode($data);
        $result = @new User(
            $userData->username,
            $userData->password,
            $userData->emailAddress,
            $userData->fullName,
            $userData->tenantId,
            $userData->enabled,
            $userData->externallyDefined,
            $userData->previousPasswordChangeTime
        );
        foreach ($userData->roles as $role) {
            $newRole = @new Role($role->name, $role->tenantId, $role->externallyDefined);
            $result->roles[] = $newRole;
        }
        return $result;
    }


    /**
     * Add or Update a user
     *
     * @param \Jaspersoft\Dto\User\User
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function addOrUpdateUser($user)
    {
            $url = self::makeUserUrl($user->tenantId, $user->username);
            $this->service->prepAndSend($url, array(200, 201), 'PUT', json_encode($user), true, 'application/json', 'application/json');
    }

	/**
	 * This function will delete a user
	 *
	 * First get the user using getUsers(), then provide the user you wish to delete
	 * as the parameter for this function.
	 *
	 * @param \Jaspersoft\Dto\User\User $user
	 */
	public function deleteUser(User $user)
    {
        $url = self::makeUserUrl($user->tenantId, $user->username);
        $this->service->prepAndSend($url, array(204), 'DELETE', null, false, 'application/json', 'application/json');
	}


    /**
     * Retrieve attributes of a user.
     *
     * @param \Jaspersoft\Dto\User\User $user
     * @param array $attributeNames
     * @return null|array
     * @throws \Exception
     */
    public function getAttributes(User $user, $attributeNames = null)
    {
        $result = array();
        $url = self::makeAttributeUrl($user->username, $user->tenantId, $attributeNames);
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
     * Create a non-existent attribute, or update an existing attribute
     *
     * @param \Jaspersoft\Dto\User\User $user
     * @param \Jaspersoft\Dto\Attribute\Attribute $attribute
     * @return bool|null
     */
    public function addOrUpdateAttribute(User $user, $attribute)
    {
        $url = self::makeAttributeUrl($user->username, $user->tenantId, null, $attribute->name);
        $data = json_encode($attribute);
        return $this->service->prepAndSend($url, array(201, 200), 'PUT', $data, false,
            'application/json', 'application/json');
    }

    /**
     * Replace all existing attributes with the provided set
     *
     * @param User $user
     * @param array $attributes
     */
    public function replaceAttributes(User $user, array $attributes)
    {
        $url = self::makeAttributeUrl($user->username, $user->tenantId);
        $data = json_encode(array('attribute' => $attributes));
        $this->service->prepAndSend($url, array(200), 'PUT', $data, 'application/json', 'application/json');
    }

    /**
     * Remove all attributes, or specific attributes from a user.
     *
     * @param \Jaspersoft\Dto\User\User $user
     * @param array $attributes
     */
    public function deleteAttributes(User $user, $attributes = null)
    {
        $url = self::makeAttributeUrl($user->username, $user->tenantId);
        if (!empty($attributes)) {
            $url .= '?' . Util::query_suffix(array('name' => $attributes));
        }
        $this->service->prepAndSend($url, array(204), 'DELETE', null, false,
            'application/json', 'application/json');
    }

}