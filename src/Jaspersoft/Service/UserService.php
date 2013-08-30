<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Role\Role;
use Jaspersoft\Dto\User\UserLookup;
use Jaspersoft\Tool\Util;
use Jaspersoft\Tool\RESTRequest;

class UserService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
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
     * @param null $searchTerm
     * @param null $organization
     * @param $requiredRoles
     * @param $hasAllRequiredRoles
     * @param $includeSubOrgs
     * @return array
     */
    public function searchUsers($searchTerm = null, $organization = null,
                                $requiredRoles = null, $hasAllRequiredRoles = null, $includeSubOrgs = true) {
        $result = array();
        $url = self::make_url($organization, null,
            array('search' => $searchTerm,
                  'requiredRole' => $requiredRoles,
                  'hasAllRequiredRoles' => $hasAllRequiredRoles,
                  'includeSubOrgs' => $includeSubOrgs));
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
		$userData->externallyDefined = ($userData->externallyDefined) ? 'true' : 'false';
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
            $result->addRole(@new Role($role->name, $role->externallyDefined));
        }
        return $result;
    }

	
    /**
     * This function adds NEW users. It will accept an array of User objects,
     * or one User object to add to the database.
     *
     *
     * @param User | array<User> $users - single User object or array of User objects to be created
     * @return bool - based on success of function
     */
    public function addUsers($users) {
        // Batch PUT is not available, recursively call this for each user provided in $users
        if (is_array($users)) {
            foreach($users as $user) {
                $this->addUsers($user);
            }
        } else {
            $url = self::make_url($users->tenantId, $users->username);
            $response = $this->service->prepAndSend($url, array(200, 201), 'PUT', json_encode($users),
                true, 'application/json', 'application/json');
            if (empty($response)) {
                return null;
            }
        }
        return true;
    }
	
	/**
	 * This function is an alias to addUsers which will also update a user
	 * NOTE: You cannot change a user's username with this functoin
	 * 
	 * @param $user User A user object that represents the updated user
	 */
	public function updateUser(User $user)
	{
		self::addUsers($user);
	}

	/**
	 * This function will delete a user
	 *
	 * First get the user using getUsers(), then provide the user you wish to delete
	 * as the parameter for this function.
	 *
	 * @param User $user - user to delete
	 * @return bool - based on success of function
	 */
	public function deleteUser(User $user) {
        $url = self::make_url($user->tenantId, $user->username);
        $this->service->prepAndSend($url, array(204), 'DELETE', null, false, 'application/json', 'application/json');
	}
	
}


?>
