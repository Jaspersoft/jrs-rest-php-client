<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Role\Role;
use Jaspersoft\Dto\User\UserLookup;
use Jaspersoft\Tool\Util;
use Jaspersoft\Client\Client;

class UserService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(Client &$client)
	{
		$this->service = $client->getService();
		$this->restUrl2 = $client->getURL();
	}
	
	private function make_url($organization, $username = null, $params = null)
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
                                $requiredRoles = null, $hasAllRequiredRoles = null, $includeSubOrgs = true, $limit = 0, $offset = 0) {
        $result = array();
        $url = self::make_url($organization, null,
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
     * @param UserLookup $userLookup
     * @return User
     */
    public function getUserByLookup(UserLookup $userLookup) {
        return $this->getUser($userLookup->username, $userLookup->tenantId);
    }

    /**
     * Request the User object for $username within $organization
     *
     * @param $username
     * @param $organization
     * @return User
     */
    public function getUser($username, $organization = null)
	{
        $url = self::make_url($organization, $username);
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
            $result->addRole(@new Role($role->name, $role->tenantId, $role->externallyDefined));
        }
        return $result;
    }


    /**
     * This function can be used to both add and update a user.
     *
     *
     * @param \Jaspersoft\Dto\User\User
     * @throws \Jaspersoft\Exception\RESTRequestException
     */
    public function addUser($user) {
            $url = self::make_url($user->tenantId, $user->username);
            $this->service->prepAndSend($url, array(200, 201), 'PUT', json_encode($user), true, 'application/json', 'application/json');
    }

	/**
	 * This function will delete a user
	 *
	 * First get the user using getUsers(), then provide the user you wish to delete
	 * as the parameter for this function.
	 *
	 * @param User $user - user to delete
	 */
	public function deleteUser(User $user) {
        $url = self::make_url($user->tenantId, $user->username);
        $this->service->prepAndSend($url, array(204), 'DELETE', null, false, 'application/json', 'application/json');
	}
	
}


?>
