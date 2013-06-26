<?php
/* ==========================================================================

 Copyright (C) 2005 - 2012 Jaspersoft Corporation. All rights reserved.
 http://www.jaspersoft.com.

 Unless you have purchased a commercial license agreement from Jaspersoft,
 the following license terms apply:

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero  General Public License for more details.

 You should have received a copy of the GNU Affero General Public  License
 along with this program. If not, see <http://www.gnu.org/licenses/>.

=========================================================================== */

namespace Jasper;

// PEAR Packages (soon to be removed)
// These libraries are old and throw standards errors, thus their import is squelched
@require_once('XML/Serializer.php');
@require_once('XML/Unserializer.php');

// Objects used by the class
require_once('Constants.php');
require_once('REST_Request.php');
require_once('User.php');
require_once('UserLookup.php');
require_once('Organization.php');
require_once('Role.php');
require_once('Attribute.php');
require_once('ResourceDescriptor.php');
require_once('JobSummary.php');
require_once('Job.php');
require_once('Permission.php');
require_once('ReportOptions.php');
require_once('ExportTask.php');
require_once('ImportTask.php');

// require_once('Execution.php');
// require_once('ExecutionRequ.php');

class JasperClient {

	protected $hostname;
	protected $port;
	protected $username;
	protected $password;
	protected $orgId;
	protected $baseUrl;
	private $restReq;
	private $restUrl;
	private $restUrl2;

	/***> INTERNAL FUNCTIONS <***/

	/**
	 * Constructor for JasperClient. All these values are required to be defined so that
	 * the client can function properly.
	 *
	 * @param string $hostname - Hostname of the JasperServer that the API is running on
	 * @param int|string $port - Port of the same server
	 * @param string $username - Username for authentication
	 * @param string $password - Password for authentication
	 * @param string $baseUrl - base URL (i.e: /jasperserver-pro or /jasperserver (community edition))
	 * @param string $orgId - organization ID, required for login within multiple tenancy
	 */
	public function __construct($hostname = 'localhost', $port = '8080', $username = null, $password = null, $baseUrl = "/jasperserver-pro", $orgId = null)
	{
		$this->hostname = $hostname;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->baseUrl = $baseUrl;
		$this->orgId = $orgId;

		$this->restReq = new \REST_Request();
		if (!empty($this->orgId)) {
			$this->restReq->setUsername($this->username .'|'. $this->orgId);
		} else {
			$this->restReq->setUsername($this->username);
		}
		$this->restReq->setPassword($this->password);
		$this->restUrl = PROTOCOL . $this->hostname . ':' . $this->port . $this->baseUrl . BASE_REST_URL;
		$this->restUrl2 = PROTOCOL . $this->hostname . ':' . $this->port . $this->baseUrl . BASE_REST2_URL;
	}

    /**
     * Internal function that prepares and send the request. This function validates that
     * the status code returned matches the $expectedCodes provided and returns a bool
     * based on that.
     *
     * @param string $url - URL to be called
     * @param array<int> $expectedCodes Array with 1 or more elements relating to status codes expected with success of function
     * @param string $verb - verb to be used
     * @param string $reqBody - The body of the request (POST/PUT)
     * @param bool $returnData - if true the responseInfo will be returned with the function
     * @param string $contentType - the content type of request body
     * @param string $acceptType - expected content type of response body
     * @throws RESTRequestException - if the status codes do not match
     * @return bool|int - true if expectedCode == statusCode; if no match, returns status code
     */
	protected function prepAndSend($url, $expectedCodes = array(200), $verb = null, $reqBody = null, $returnData = false,
                                   $contentType = 'application/xml', $acceptType = 'application/xml') {
		$this->restReq->flush();
		$this->restReq->setUrl($url);
		if ($verb !== null) {
			$this->restReq->setVerb($verb);
		}
		if ($reqBody !== null) {
			$this->restReq->buildPostBody($reqBody);
		}
		if (!empty($contentType)) {
			$this->restReq->setContentType($contentType);
		}
		if(!empty($acceptType)) {
			$this->restReq->setAcceptType($acceptType);
		}

		$this->restReq->execute();
		$statusCode = $this->restReq->getResponseInfo();
		$responseBody = $this->restReq->getResponseBody();
		$statusCode = $statusCode['http_code'];

        // An exception is thrown here if the expected code does not match the status code in the response
		if (!in_array($statusCode, $expectedCodes)) {
			if(!empty($responseBody)) {
				throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode . ' Body of response: ' . strip_tags($responseBody));
			} else {
				throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode);
			}
		}
		if($returnData == true) {
			return $this->restReq->getResponseBody();
		}
		return true;
	}


    /**
     * This function creates a multipart/form-data request and sends it to the server.
     * this function should only be used when a file is to be sent with a request (PUT/POST).
     *
     * @param string $url - URL to send request to
     * @param int|string $expectedCode - HTTP Status Code you expect to receive on success
     * @param string $verb - HTTP Verb to send with request
     * @param string $reqBody - The body of the request if necessary
     * @param array $file - An array with the URI string representing the image, and the filepath to the image. (i.e: array('/images/JRLogo', '/home/user/jasper.jpg') )
     * @param bool $returnData - whether or not you wish to receive the data returned by the server or not
     * @return array - Returns an array with the response info and the response body, since the server sends a 100 request, it is hard to validate the success of the request
     */
	protected function multipartRequestSend($url, $expectedCode = 200, $verb = 'PUT_MP', $reqBody = null, $file = null,
                                            $returnData = false) {
		$expectedCode = (integer) $expectedCode;
		$this->restReq->flush();
		$this->restReq->setUrl($url);
		$this->restReq->setVerb($verb);
		if (!empty($reqBody)) {
			$this->restReq->buildPostBody($reqBody);
		}
		if (!empty($file)) {
			$this->restReq->setFileToUpload($file);
		}
		$this->restReq->execute();
		$response = $this->restReq->getResponseInfo();
		$responseBody = $this->restReq->getResponseBody();
		$statusCode = $response['http_code'];

		return array($statusCode, $responseBody);
	}

    /**
     * This function will create an HTTP query string that may include repeated values
     * @param $params
     * @return string
     */
    protected static function query_suffix($params) {
        $url = http_build_query($params, null, '&');
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $url);
    }

	/***> ATTRIBUTE SERVICE <***/

    /**
     * attributeServiceURL - Builds a URL to be used with an attribute service
     *
     * @param $username - Username of interest
     * @param $tenantID - Organization user is a part of
     * @param $attributeNames - Array of attribute names you seek
     * @return string
     */
    protected function attributeServiceURL($username, $tenantID = null, $attributeNames = null) {
        if (!empty($tenantID)) {
            $url = $this->restUrl2 . ORGANIZATION_2_BASE_URL . '/' . $tenantID . USER_2_BASE_URL . '/' . $username .
                ATTRIBUTE_2_BASE_URL;
        } else {
            $url = $this->restUrl2 . USER_2_BASE_URL . '/' . $username . ATTRIBUTE_2_BASE_URL;
        }
        // Allow for parametrized attribute searches
        if (!empty($attributeNames)) {
            $url .= '?' . JasperClient::query_suffix(array('name' => $attributeNames));
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
    public function getAttributes(User $user, $attributeNames = null) {
        $result = array();
        $url = $this->attributeServiceURL($user->getUsername(), $user->getTenantId(), $attributeNames);
        $data = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');

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
     * Combine two arrays of attribute objects. This function will replace attributes in $a with those in $b if the
     * same key is found in each array. Otherwise, they will be combined.
     *
     * @param $a
     * @param $b
     * @return array
     */
    protected static function combineAttributeArrays($a, $b) {
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

	/** DEPRECATED -> use addAttributes or updateAttributes
     * Create new attributes, or replace existing attributes.
     *
     * This function will add attributes to a user in addition to those already in place. If an attribute with the same
     * name already exists, it will be replaced by the new value supplied.
     *
     * @deprecated
	 * @param User $user - user object of user whose attributes you wish to change
	 * @param array<Attribute> $attributes - array of attributes, or a single attribute object
	 * @throws Exception - if HTTP returns an error status code
	 */
    public function postAttributes(User $user, $attributes) {
        $this->addAttributes($user, $attributes);
    }

    /**
     * Replace all existing attributes for a user with those defined in the attributes array parameter
     *
     * @param User $user
     * @param $attributes - An array of attribute objects (must be array)
     */
    public function updateAttributes(User $user, $attributes) {
        $url = $this->attributeServiceURL($user->getUsername(), $user->getTenantId());
        $data = json_encode(array('attribute' => $attributes));
        $this->prepAndSend($url, array(201, 200), 'PUT', $data, 'application/json', 'application/json');
    }

    /**
     * Create new attributes, or replace existing attributes.
     *
     * This function will add attributes to a user in addition to those already in place. If an attribute with the same
     * name already exists, it will be replaced by the new value supplied.
     * @param $user - User object to modify
     * @param $attributes - An array or single attribute object to add or update on a user
     */
    public function addAttributes(User $user, $attributes) {
        $url = $this->attributeServiceURL($user->getUsername(), $user->getTenantId());
        if (!is_array($attributes))
            $attributes = array($attributes);
        $existing_attributes = $this->getAttributes($user);
        if (is_array($existing_attributes) && ($existing_attributes) > 0)
            $attributes = $this::combineAttributeArrays($existing_attributes, $attributes);
        $data = json_encode(array('attribute' => $attributes));
        $this->prepAndSend($url, array(201, 200), 'PUT', $data, 'application/json', 'application/json');
    }

	/***> USER SERVICE <***/

    protected function userServiceURL($organization, $username = null, $params = null) {
        if(!empty($organization)) {
            $url = $this->restUrl2 . ORGANIZATION_2_BASE_URL . '/' . $organization . USER_2_BASE_URL;
        } else {
            $url = $this->restUrl2 . USER_2_BASE_URL;
        }
        if (!empty($username)) {
            $url .= '/' . $username;
            // Return early because no params can be set with single-user operations
            return $url;
        }
        if (!empty($params)) {
            $url .= '?' . JasperClient::query_suffix($params);
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
        $url = $this->userServiceURL($organization, null,
            array('search' => $searchTerm,
                  'requiredRole' => $requiredRoles,
                  'hasAllRequiredRoles' => $hasAllRequiredRoles,
                  'includeSubOrgs' => $includeSubOrgs));
        $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
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
    public function getUser($username, $organization = null) {
        $url = $this->userServiceURL($organization, $username);
        $data = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
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
            $result->addRole(@new Role($role->name, $role->externallyDefined));
        }
        return $result;
    }

    /** DEPRECATED -> use searchUsers and getUser
     * Retrieve users from the server.
     *
     * Result will always be an array of zero or more User objects.
     * Search term can be any part of a username, and all users matching the searchTerm will be returned
     *
     * If no searchTerm is provided, all users will be returned.
     *
     * @deprecated
     * @param $searchTerm - part of user name you would like to search for
     * @param $organization
     * @return Array<User>
     * @throws Exception if HTTP request fails
     */
	public function getUsers($searchTerm = null, $organization = null) {
        $result = array();
        $search = $this->searchUsers($searchTerm, $organization);
        foreach ($search as $foundUser) {
            $result[] = $this->getUserByLookup($foundUser);
        }
        return $result;
	}

	/**
     * DEPRECATED -> use addUsers
	 *
	 * This function is an alias to addUsers
	 *
     * @deprecated
	 * @param User | array<User> $users - single User object or array of User objects to be created
	 * @return bool - based on success of function
	 */
	public function putUsers($users) {
        $this->addUsers($users);
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
            $url = $this->userServiceURL($users->tenantId, $users->username);
            $response = $this->prepAndSend($url, array(200, 201), 'PUT', json_encode($users),
                true, 'application/json', 'application/json');
            if (empty($response)) {
                return null;
            }
        }
        return true;
    }

    /** DEPRECATED -> use addUsers
     * POST User.
     *
     * This function is now an alias to the PUT function. The implementation has changed since earlier
     * versions of the REST API so that PUT creates and updates a user.
     *
     * @deprecated
     * @param User $user - single User object
     * @return bool - based on success of function
     */
	public function postUser(User $user) {
        $this->putUsers($user);
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
        $url = $this->userServiceURL($user->tenantId, $user->username);
        $this->prepAndSend($url, array(204), 'DELETE', null, false, 'application/json', 'application/json');
	}

	/***> ORGANIZATION SERVICE <***/
    protected function organizationServiceURL($organization = null, $params = null) {
        $url = $this->restUrl2 . ORGANIZATION_2_BASE_URL;
        if (!empty($organization)) {
            $url .= '/' . $organization;
            return $url;
        }
        if (!empty($params))
            $url .= '?' . JasperClient::query_suffix($params);
        return $url;
    }

	/** DEPRECATED -> use searchOrganization
     * This function retrieves an organization and its information by ID.
	 *
     * @deprecated
	 * @param string $org - organization id (i.e: "organization_1")
	 * @param bool $listSub - If this is true, suborganizations are only retrieved
	 * @return Organization - object that represents organization & its data
	 * @throws Exception - if HTTP request doesn't respond as expected
	 */
	public function getOrganization($org, $listSub = false) {
        if (!$listSub)
            $url = $this->organizationServiceURL($org);
        else
            $url = $this->organizationServiceURL(null, array('rootTenantId' => $org));
        $data = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        $resp = json_decode($data);

        if (empty($resp))
            return null;

        if (isset($resp->organization)) {
        foreach ($resp->organization as $org) {
            $result[] = @new Organization(
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

        } else {
            $result = @new Organization(
                $resp->alias,
                $resp->id,
                $resp->parentId,
                $resp->tenantName,
                $resp->theme,
                $resp->tenantDesc,
                $resp->tenantFolderUri,
                $resp->tenantNote,
                $resp->tenantUri
            );
            return $result;
        }
        if (is_array($result) && count($result) > 1)
            return $result;
        else
            return $result[0];
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
                                       $limit = null, $offset = null) {
        $result = array();
        $url = $this->organizationServiceURL(null, array(
            'q' => $query,
            'rootTenantId' => $rootTenantId,
            'maxDepth' => $maxDepth,
            'includeParents' => $includeParents,
            'limit' => $limit,
            'offset' => $offset));
        $resp = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
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
     * DEPRECATED -> use createOrganization
     * This function creates an organization on the server you must provide a
	 * built organization object to it as a parameter.
	 *
     * @deprecated
	 * @param Organization $org - organization object to add
	 * @return bool - based on success of request
	 * @throws Exception - if HTTP request doesn't signify success
	 */
	public function putOrganization(Organization $org) {
        $url = $this->organizationServiceURL();
		$data = json_encode($org);
        if ($this->prepAndSend($url, array(201), 'POST', $data, false, 'application/json', 'application/json'))
            return true;
        return false;
	}

    /**
     * This function creates a new organization. If you do not wish for default users to be created
     * supply false as the second parameter.
     *
     * @param Organization $org
     * @param bool $defaultUsers
     * @return bool
     */
    public function createOrganization(Organization $org, $defaultUsers = true) {
        $url = $this->organizationServiceURL(null, array('defaultUsers' => $defaultUsers));
        $data = json_encode($org);
        if ($this->prepAndSend($url, array(201), 'POST', $data, false, 'application/json', 'application/json'))
            return true;
        return false;
    }

	/**
     * Delete an organization.
	 *
	 * @param Organization $org - organization object
	 * @return bool - based on success of request
	 * @throws Exception - if HTTP request doesn't succeed
	 */
	public function deleteOrganization(Organization $org) {
        $url = $this->organizationServiceURL($org->getId());
		if($this->prepAndSend($url, array(200, 204), 'DELETE')) {
			return true;
		}
		return false;
	}

	/** DEPRECATED -> use updateOrganization
     * Update an organization.
	 *
	 * It is suggested that you use the getOrganization function to retrieve an object to be updated
	 * then from there you can modify it using the set functions, and then provide it to this function
	 * to be updated on the server side. Integrity checks are not made through this wrapper, but
	 * any errors retrieved by the server do raise an Exception.
	 *
     * @deprecated
	 * @param Organization $org - organisation object
	 * @return bool - based on success of request
	 * @throws Exception - if HTTP request doesn't succeed
	 */
	public function postOrganization(Organization $org) {
        $this->updateOrganization($org);
	}

    /**
     * This function updates an existing organization. Supply an organization object with the expected changes.
     *
     * @param Organization $org
     * @return bool
     */
    public function updateOrganization(Organization $org) {
        $url = $this->organizationServiceURL($org->getId());
        $data = json_encode($org);
        if ($this->prepAndSend($url, array(201, 200), 'PUT', $data, false, 'application/json', 'application/json'))
            return true;
        return false;
    }

	/***> ROLE SERVICE <***/

    protected function roleServiceURL($organization = null, $roleName = null, $params = null) {
        if(!empty($organization))
            $url = $this->restUrl2 . ORGANIZATION_2_BASE_URL . '/' . $organization . ROLE_2_BASE_URL;
        else
            $url = $this->restUrl2 . ROLE_2_BASE_URL;
        if (!empty($roleName))
            $url .= '/' . $roleName;
        // If a role name is defined, no parameters are expected
        else if (!empty($params))
            $url .= '?' . JasperClient::query_suffix($params);
        return $url;
    }

	/**
     * DEPRECATED -> Use getRole to get a single role, or getManyRoles to get multiple roles based on criterion
     * Retrieve existing roles.
	 *
	 * Returns all roles that match $searchTerm (results can be >1). If you wish to retrieve all roles in a
	 * suborganization, set $searchTerm to an empty string and define the suborganization
	 * i.e: $jasperclient->getRoles('', 'organization_1').
	 *
     * @deprecated
	 * @param string $searchTerm - search the roles for matching values - returns multiple if multiple matches
	 * @param string $tenantId - if the role is part of an organization, be sure to add tenantId
	 * @return Role role - role object that represents the role
	 * @throws Exception - if http request doesn't succeed
	 */
	public function getRoles($searchTerm = null, $tenantId = null) {
        $result = array();
        $url = $this->roleServiceURL($tenantId, null, array('search' => $searchTerm));

        if ($data = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json')) {
            $roles = json_decode($data);
            foreach ($roles->role as $role)
                $result[] = @new Role($role->name, $role->tenantId, $role->externallyDefined);
        }
        if (count($result) == 1)
            return $result[0];
        return $result;
    }

    /**
     * Search for many or all roles on the server.
     * You can search by organization as well.
     *
     * @param null $organization
     * @param null $includeSubOrgs
     * @return array
     */
    public function getManyRoles($organization = null, $includeSubOrgs = null) {
        $result = array();
        $url = $this->roleServiceURL($organization, null, array('includeSubOrgs' => $includeSubOrgs));
        $data = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        $data = (!empty($data)) ? json_decode($data, true) : null;
        if ($data === null)
            return $result;
        foreach ($data['role'] as $r)
            $result[] = @new Role($r['name'], $r['tenantId'], $r['externallyDefined']);
        return $result;
    }

    /** Get a Role by its name
     *
     * @param $roleName
     * @param $organization - Name of organization role belongs to
     * @return Role
     */
    public function getRole($roleName, $organization = null) {
        $url = $this->roleServiceURL($organization, $roleName);
        $resp = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        $data = json_decode($resp);
        return @new Role($data->name, $data->tenantId, $data->externallyDefined);
    }

    /**
     * Add a new role.
     *
     * Provide a role object that represents the role you wish to add.
     *
     * @param Role $role - role to add (1 at a time)
     * @return bool - based on success of function
     * @throws Exception - if http request doesn't succeed
     */
    public function createRole(Role $role) {
        $url = $this->roleServiceURL($role->getTenantId(), $role->getRoleName());
        if ($this->prepAndSend($url, array(201, 200), 'PUT', json_encode($role), false, 'application/json', 'application/json'))
            return true;
        return false;
    }

	/** DEPRECATED -> use createRole
     * Add a new role.
	 *
	 * Provide a role object that represents the role you wish to add.
	 *
     * @deprecated
	 * @param Role $role - role to add (1 at a time)
	 * @return bool - based on success of function
	 * @throws Exception - if http request doesn't succeed
	 */
	public function putRole(Role $role) {
        $this->createRole($role);
	}

    /**
     * Remove a role currently in existence.
     *
     * Provide the Role object of the role you wish to remove. Use getRole() to retrieve Roles.
     *
     * @param Role $role
     * @internal param string $roleName - Name of the role to DELETE
     * @return bool - based on success of function
     */
	public function deleteRole(Role $role) {
        $url = $this->roleServiceURL($role->getTenantId(), $role->getRoleName());
        if ($this->prepAndSend($url, array(204, 200), 'DELETE'))
            return true;
        return false;
	}

    /**
     * Update a role currently in existence.
     *
     * Provide the Role object of the role you wish to change, then a string of the new name
     * you wish to give the role. You can optionally provide a new tenantId if you wish to change
     * that as well.
     *
     * @param Role $role - Role object to be changed
     * @param string $oldName - previous name for the role
     * @return bool
     * @throws Exception - if http request does not succeed
     */
    public function updateRole(Role $role, $oldName = null) {
        $url = $this->roleServiceURL($role->getTenantId(), $oldName);
        if ($this->prepAndSend($url, array(200, 201), 'PUT', json_encode($role), false, 'application/json', 'application/json'))
            return true;
        return false;
    }

    /** DEPRECATED -> use updateRole
     *
     * @deprecated
     * @param Role $role - Role object to be changed
     * @param string $oldName - previous name for the role
     * @return bool
     * @throws Exception - if http request does not succeed  */
	public function postRole(Role $role, $oldName = null) {
        $this->updateRole($role, $oldName);
	}

	/***> REPORT SERVICE <***/
	/**
	 * This function runs and retrieves the binary data of a report.
     *
	 * @param string $uri - URI for the report you wish to run
	 * @param string $format - The format you wish to receive the report in (default: pdf)
	 * @param string $page - Request a specific page
	 * @param string $attachmentsPrefix - a URI to prefix all image attachment sources with (must include trailing slash if needed)
	 * @param array $inputControls - associative array of key => value for any input controls
	 * @return string - the binary data of the report to be handled by external functions
	 */
	public function runReport($uri, $format = 'pdf', $page = null, $attachmentsPrefix = null, $inputControls = null) {
		$url = $this->restUrl2 . REPORTS_BASE_URL . $uri . '.' . $format;
		if(!(empty($page) && empty($inputControls))) {
			$url .= '?' . preg_replace('/%5B([0-9]{1,})%5D/', null, http_build_query(array('page' => $page) + array('attachmentsPrefix' => $attachmentsPrefix) + (array) $inputControls));
		}
		$binary = $this->prepAndSend($url, array(200), 'GET', null, true);
		return $binary;
	}

	/***> REPOSITORY SERVICE <***/

	/**
	 * This function retrieves the Resources from the server.
     * It returns an array consisting of ResourceDescriptor objects that represent the data.
	 *
	 * @param string $uri
	 * @param string $query
	 * @param string $wsType
	 * @param string $recursive
	 * @param string $limit
	 * @return array<ResourceDescriptor>
	 */
	public function getRepository($uri = null, $query = null, $wsType = null, $recursive = null, $limit = null) {
		$url = $this->restUrl . '/resources';
		$suffix = http_build_query(array('q' => $query, 'type' => $wsType, 'recursive' => $recursive, 'limit' => $limit));
		$result = array();

		if(!empty($uri)) { $url .= $uri; }
		if (!empty($suffix)) { $url .= '?' . $suffix; }
		$data = $this->prepAndSend($url, array(200), 'GET', null, true);
		$xml = new \SimpleXMLElement($data);
		foreach ($xml->resourceDescriptor as $rd) {
			$obj = ResourceDescriptor::createFromXML($rd->asXML());
			$result[] = $obj;
		}
		return $result;
	}

	/**
     * This function retrieves a resource descriptor for a specified resource at $path on the server.
	 * If you wish to supply information to the input controls you can supply the data to the $p and $pl arguments.
	 *
	 * @param string $path
	 * @param bool $fileData - set to true if you wish to receive the binary data of the resource (i.e: with images)
	 * @param string $ic_get_query_data - the datasource to query
	 * @param string $p - single select parameters | example: array(parameter_name, value)
	 * @param string $pl - multi select parameters | example: array(parameter_name, array(value1, value2, value3))
	 * @return \Jasper\ResourceDescriptor
	 */
	public function getResource($path, $fileData = false, $ic_get_query_data = null, $p = null, $pl = null) {
		$url = $this->restUrl . '/resource' . $path;
		$suffix = ($fileData) ? http_build_query(array('fileData' => 'true')) : null;
		$suffix .= http_build_query(array('IC_GET_QUERY_DATA' => $ic_get_query_data));
		if (!empty($p)) { $suffix .= http_build_query($p); }
		if (!empty($pl)) {
			$param = array_shift($pl);
			if(!empty($suffix)) { $suffix .= '&'; }
			// http_build_query will take the numerical array and transfer the index keys ([0], [1], etc) into text
			// for the URL. This is undesirable in this scenario, so we use a regular expression to remove the indices
			$suffix .= preg_replace('/%5B([0-9]{1,})%5D/', null, http_build_query(array('PL_' . $param => $pl)));
		}
		if (!empty($suffix)) { $url .= '?' . $suffix; }
		$data = $this->prepAndSend($url, array(200), 'GET', null, true);
		if ($fileData === true) {
			return $data;
		} else {
		return ResourceDescriptor::createFromXML($data);
		}
	}

    /**
     * Upload a new resource to the repository.
     *
     * Note: first create a ResourceDescriptor object.
     *
     * @param string $path
     * @param ResourceDescriptor $rd - ResourceDescriptor object that relates to the resource being uploaded
     * @param string $file - File path to file being uploaded
     * @throws RESTRequestException
     * @return bool
     */
	public function putResource($path, ResourceDescriptor $rd, $file = null) {
		$url = $this->restUrl . '/resource' . $path;
		$statusCode = null;
		if (!empty($file)) {
            $xml = $rd->toXML();
            $uri = $rd->getUriString();
            $body = array(
                'ResourceDescriptor' => $xml,
                $uri => '@'.$file.';filename='.basename($file)
            );
			$data = $this->multipartRequestSend($url, 201, 'PUT_MP', $body, null, true);
			$statusCode = $data[0];
		} else {
			$data = $this->prepAndSend($url, array(201), 'PUT', $rd->toXML(), null, true);
			if ($data) { return true; }
		}
		// Note: the prepAndSend function handles the following error checking within itself, however with a multipart request
		// status code 100 is sometimes returned for more data, when handling through this function, the final status code is returned
		// and can be properly validated
		if ($statusCode !== 201) {
			throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode);
		} else {
			return true;
		}
	}

    /**
     * Update a resource that is already in existence by providing a new ResourceDescriptor defining the object at the URI provided.
     *
     * @param string $path - The path to the resource you wish to change
     * @param ResourceDescriptor $rd - a ResourceDescriptor object that correlates to the object you wish to modify (with the changes)
     * @param string $file - full file path to the image you wish to upload
     * @throws RESTRequestException
     * @return bool - based on success of function
     */
	public function postResource($path, ResourceDescriptor $rd, $file = null) {
		$url = $this->restUrl . '/resource' . $path;
		$statusCode = null;
		if (!empty($file)) {
            $xml = $rd->toXML();
            $uri = $rd->getUriString();
            $body = array(
                'ResourceDescriptor' => $xml,
                $uri => '@'.$file.';filename='.basename($file)
            );
			$data = $this->multipartRequestSend($url, 200, 'POST_MP', $body, null, true);
			$statusCode = $data[0];
		} else {
			$data = $this->prepAndSend($url, array(200), 'POST', $rd->toXML(), null, true);
			if($data) { return true; }
		}
		if ($statusCode !== 200) {
			throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode);
		} else {
			return true;
		}
	}

	/**
     * This function deletes a resource.
     *
	 * Note: it will only succeed if certain requirements are met. See "Web Services Guide" to see these requirements.
	 *
	 * @param string $path - path to resource to be deleted
	 * @return bool
	 */
	public function deleteResource($path) {
		$url = $this->restUrl . '/resource' . $path;
		$result = $this->prepAndSend($url, array(200), 'DELETE');
		return $result;
	}

	/***> JOB/JOBSUMMARY SERVICE <***/

    protected function jobServiceURL($params = null) {
        $url = $this->restUrl2 . JOB_2_BASE_URL;
        if (!empty($params))
            $url .= '?' . JasperClient::query_suffix($params);
        return $url;
    }

    /** DEPRECATED -> use searchJobs
     * Retrieve scheduled Jobs
     *
     * You can search either by URI or Name. If you are searching by job name, set the second argument to true.
     *
     * @deprecated
     * @param string $query - your search term
     * @param bool $searchByName - true = search by Report Name, false = search by report URI (default when unset)
     * @return \Jasper\JobSummary|NULL
     */
	public function getJobs($query = null, $searchByName = false) {
        $result = array();
        if ($searchByName)
            $url = $this->jobServiceURL(array('label' => $query));
        else
            $url = $this->jobServiceURL(array('reportUnitURI' => $query));
        $resp = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (empty($resp))
            return $result;
        $jobs = json_decode($resp);
        foreach($jobs->jobsummary as $job) {
            $result[] = @new JobSummary(
                $job->id,
                $job->label,
                $job->reportUnitURI,
                $job->version,
                $job->owner,
                $job->state->value,
                $job->state->nextFireTime,
                $job->state->previousFireTime
            );
        }
        return $result;
	}

    /**
     * Search for scheduled jobs.
     *
     * @param null $reportUnitURI - URI of the report (optional)
     * @param null $owner - Search by user who created job
     * @param null $label - Search by job label title
     * @param null $example - Search by any field of Job description in JSON format (i.e: {"outputFormats" : ["RTF", "PDF" ]} )
     * @param null $startIndex - Start at this number (pagination)
     * @param null $rows - Number of rows in a block (pagination)
     * @param null $sortType - How to sort by column, must be any of the following:
     * NONE, SORTBY_JOBID, SORTBY_JOBNAME, SORTBY_REPORTURI, SORTBY_REPORTNAME, SORTBY_REPORTFOLDER,
     * SORTBY_OWNER, SORTBY_STATUS, SORTBY_LASTRUN, SORTBY_NEXTRUN
     * @param bool $ascending - Sorting direction, ascending if true, descending if false
     * @return array|NULL
     */
    public function searchJobs($reportUnitURI = null, $owner = null, $label = null, $example = null, $startIndex = null,
        $rows = null, $sortType = null, $ascending = null)
    {
        $result = array();
        $url = $this->jobServiceURL(array(
            'reportUnitURI' => $reportUnitURI,
            'owner' => $owner,
            'label' => $label,
            'example' => $example,
            'startIndex' => $startIndex,
            'numberOfRows' => $rows,
            'sortType' => $sortType,
            'isAscending' => $ascending
        ));

        $resp = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        if (empty($resp))
            return $result;
        $jobs = json_decode($resp);
        foreach($jobs->jobsummary as $job) {
            $result[] = @new JobSummary(
                $job->id,
                $job->label,
                $job->reportUnitURI,
                $job->version,
                $job->owner,
                $job->state->value,
                $job->state->nextFireTime,
                $job->state->previousFireTime
            );
        }
        return $result;
    }

	/**
     * Request a job object from server by JobID.
	 *
     * JobID can be found using getId() from an array of jobs returned by the getJobs function.
	 *
	 * @param int|string $id - the ID of the job you wish to know more about
	 * @return Job object
	 */
	public function getJob($id) {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return new Job(json_decode($data, true));
	}

	/**
     * Delete a scheduled task.
	 *
     * This function will delete a job that is scheduled.
     * You must supply the Job's ID to this function to delete it.
	 *
	 * @param int|string $id - can be retrieved using getId() on a JobSummary object
	 * @return bool - based on success of function
	 */
	public function deleteJob($id) {
		$url = $this->restUrl2 . JOB_2_BASE_URL . '/' . $id;
		$data = $this->prepAndSend($url, array(200), 'DELETE');
		if ($data)
            return true;
		return false;
	}

	/**
     * Get the State of a Job.
     *
	 * This function returns an array with state values
	 *
	 * @param int|string $id - can be retrieved using getId() on a JobSummary object
	 * @return unknown
	 */
	public function getJobState($id) {
		$url = $this->restUrl2 . '/jobs/' . $id . '/state';
		$data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return json_decode($data, true);
	}

	/**
     * Pause a job, all jobs, or multiple jobs.
	 *
	 * @param string|array|int|null $jobsToStop - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs.
	 * @return bool - based on success of function
	 */
	public function pauseJob($jobsToStop = null) {
		$url = $this->restUrl2 . '/jobs/pause';
        $body = json_encode(array("jobId" => (array) $jobsToStop));
		$data = $this->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
		if ($data) { return true; }
		return false;
	}

	/**
     * Resume a job, all jobs, or multiple jobs.
	 *
	 * @param string|array|int|null $jobsToResume - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs
	 * @return bool - based on success of function
	 */
	public function resumeJob($jobsToResume = null) {
		$url = $this->restUrl2 . '/jobs/resume';
        $body = json_encode(array("jobId" => (array) $jobsToResume));
        $data = $this->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
        if ($data) { return true; }
        return false;
	}

	/**
     * Place a new job on the server.
	 *
	 * Create a fully defined ResourceDescriptor and use this function to place it on the server. `id` does not need to be set as the server
	 * will assign a unique id to the object. This function returns a corresponding new Job object complete with the assigned id.
	 *
	 * @param Job $job
	 * @return int - this function returns the ID for the Job object you just created
	 */
	public function putJob(Job $job) {
		$url = $this->restUrl2 . '/jobs';
		$data = $this->prepAndSend($url, array(200), 'PUT', $job->toXML(), true); // For some reason PUT returns 200, this may change to 201 in the future
		$result = Job::createFromXML($data);
		return $result->id;
	}

	/**
     * Update a job.
	 *
	 * After grabbing a job using getJob() you can modify the values of the job object so it represents the changes you wish to make. Then
	 * using this function you can update the job definition on the server.
	 *
	 * @param Job $job - Job object representing updated job -- `id` must match ID of old job
	 * @return bool - based on success of function
	 */
	public function postJob(Job $job) {
		$url = $this->restUrl2 . '/jobs/' . $job->id;
		$data = $this->prepAndSend($url, array(200), 'POST', $job->toXML(), true);
		return $data;
	}

	/**
     * Retrieve permissions about a URI.
	 *
     * Your result will always be an array of 0 or more items.
	 *
	 * @param string $uri
	 * @return array<Permission>
	 */
	public function getPermissions($uri) {
		$url = $this->restUrl . '/permission' . $uri;
		$data = $this->prepAndSend($url, array(200), 'GET', null, true);
		return Permission::createFromXML($data);
	}

	/**
     * PUT/POST Permissions.
	 *
     * This function updates the permissions for a URI.
	 *
	 * @param string $uri
	 * @param array<Permission> $permissions
	 * @return bool
	 */
	public function updatePermissions($uri, $permissions) {
		$url = $this->restUrl . '/permission' . $uri;
		$body = Permission::createXMLFromArray($permissions);
		$data = $this->prepAndSend($url, array(200), 'PUT', $body);
		if ($data) { return true; }
		return false;
	}

    /**
     * Remove an already existing permission.
     *
     * Simply provide the permission object you wish to delete. (use getPermissions to fetch existing permissions).
     *
     * @param Permission $perm - object correlating to permission to be deleted.
     * @throws RESTRequestException
     * @return bool - based on success of function
     */
	public function deletePermission(Permission $perm) {
		$url = $this->restUrl . '/permission' . $perm->getUri() . '?';
		$recipient = $perm->getPermissionRecipient();

		if($recipient instanceof User) {
			$url .= http_build_query(array('users' => $recipient->getUsername()));
		} elseif ($recipient instanceof Role) {
			$url .= http_build_query(array('roles' => $recipient->getRoleName()));
		} else {
			throw RESTRequestException('Unacceptable permissionRecipient in Permission object');
		}
		$data = $this->prepAndSend($url, array(200), 'DELETE', null);
		return $data;
	}

	/**
	 * Using this function you can request the report options for a report.
	 *
	 * @param string $uri
	 * @return Array<\Jasper\ReportOptions>
	 */
	public function getReportOptions($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return ReportOptions::createFromJSON($data);
	}

	/**
	 * This function will request the possible values and data behind all the input controls of a report.
     *
	 * @param string $uri
	 * @return Array<\Jasper\InputOptions>
	 */
	public function getReportInputControls($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/inputControls/values';
		$data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return InputOptions::createFromJSON($data);
	}

	/**
	 * Update or Create new Report Options.
     *
     * The argument $controlOptions must be an array in the following form:
     *
	 * array('key' => array('value1', 'value2'), 'key2' => array('value1-2', 'value2-2'))
     *
	 * Note that even when there is only one value, it must be encapsulated within an array.
	 *
	 * @param string $uri
	 * @param array<string> $controlOptions
	 * @param string $label
	 * @param string $overwrite
	 * @return bool
	 */
	public function updateReportOptions($uri, $controlOptions, $label, $overwrite) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$url .= '?' . http_build_query(array('label' => utf8_encode($label), 'overwrite' => $overwrite));
		$body = json_encode($controlOptions);
		$data = $this->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
		return $data;
	}

	/**
	 * Remove a pre-existing report options. Provide the URI and Label of the report options you wish to remove.
	 * this function is limited in its ability to accept labels with whitespace. If you must delete a report option with whitespace
  	 * in the label name, use the deleteResource function instead. Using the URL to the report option.
     	 *
	 * @param string $uri
	 * @param string $optionsLabel
	 * @return bool
	 */
	public function deleteReportOptions($uri, $optionsLabel) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options/' . $optionsLabel;
		$data = $this->prepAndSend($url, array(200), 'DELETE', null, false);
		return $data;
	}

    /** This function returns information about the server in an associative array.
     * Information provided is:
     *
     * - Date/Time Formatting Patterns
     * - Edition
     * - Version
     * - Build
     * - Features
     * - License type and expiration
     *
     * @return array
     */
    public function info() {
        $url = $this->restUrl2 . '/serverInfo';
        $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($data, true);
    }

    /*** Import/Export Service ***/


    /** This function begins an export task on the server. You must be authorized as a superuser to use these services
     *
     *
     * @param $et - ExportTask object defining the exporting you want to do
     * @return array metadata of export job
     *
     */
    public function startExportTask(ExportTask $et) {
        $url = $this->restUrl2 . EXPORT_BASE_URL;
        $data = $this->prepAndSend($url, array(200), 'POST', json_encode($et), true, 'application/json', 'application/json');
        return json_decode($data, true);
    }

    /** Retrieve the state of your export request
     *
     *
     * @param $id - the ID of your export request
     * @return array - Associative array containing the status and message for your request
     */
    public function getExportState($id) {
        $url = $this->restUrl2 . EXPORT_BASE_URL . '/' . $id . '/state';
        $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($data, true);
    }

    /**
     * Fetch the binary data of the report. This can only be called once before the server recycles the export request
     *
     * The filename parameter determines the headers sent by the server describing the file.
     *
     * @param $id
     * @param string $filename
     * @return binary data
     */
    public function fetchExport($id, $filename = 'export.zip') {
        $url = $this->restUrl2 . EXPORT_BASE_URL . '/' . $id . '/' . $filename;
        $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/zip');
        return $data;
    }

    /** Begin an import task
     *
     * @param $it ImportTask object defining the import to be done
     * @param $file_data - Binary contents of ZIP file you wish to upload. use file_get_contents() to produce from stored file
     * @return string - ID of the import task that will be completed
     */
   public function startImportTask(ImportTask $it, $file_data) {
       $url = $this->restUrl2 . IMPORT_BASE_URL . '?' . JasperClient::query_suffix($it->queryData());
       $data = $this->prepAndSend($url, array(200, 201), 'POST', $file_data, true, 'application/zip', 'application/json');
       return json_decode($data, true);
   }

    /** Obtain the state of an ongoing import task
     *
     * @param $id
     * @return mixed
     */
    public function getImportState($id) {
       $url = $this->restUrl2 . IMPORT_BASE_URL . '/' . $id . '/state';
       $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
       return json_decode($data, true);
   }

    /*** Query Executor Service ***/

    /** This function will execute a query on a data source or domain, and return the results of such query
     *
     * @param $sourceUri - The URI for the data source or domain the query is to be executed on
     * @param $query - String query to be executed on data source/domain
     * @return array
     */
    public function executeQuery($sourceUri, $query) {
        $url = $this->restUrl2 . QUERY_EXECUTOR_BASE_URL . $sourceUri;
        $data = $this->prepAndSend($url, array(200), 'POST', $query, true, 'text/plain', 'application/json');
        return json_decode($data, true);
    }

/* These functions are not yet fully implemented //


    protected function reportExecutionServiceURL($id = null, $status = false, $exports = false, $exportOutput = null,
                                                 $outputResource = false, $attachments = false, $params = null) {
        $url = $this->restUrl2 . REPORT_EXECUTIONS_BASE_URL;
        if (!empty($params)) {
            $url .= '?' . JasperClient::query_suffix($params);
            return $url; // If params are set, there is nothing else that can be done.
        }
        if (!empty($id))
            $url .= '/' . $id;
        if (!empty($exports))
            $url .= EXPORTS_FLAG;
        if (!empty($status))
            $url .= STATUS_FLAG;
        // continue here
        return $url;
    }

    public function searchReportExecutions($uri = null, $id = null, $label = null, $username = null, $timeFrom = null,
                                           $timeTo = null) {
        $url = $this->restUrl2 . '?' . JasperClient::query_suffix(array(
                'reportURI' => $uri,
                'jobID' => $id,
                'jobLabel' => $label,
                'userName' => $username,
                'fireTimeFrom' => $timeFrom,
                'fireTimeTo' => $timeTo
            ));
        $resp = $this->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($resp, true);
    }

    public function requestReportExecution(ExecutionRequest $req) {
        $url = $this->restUrl2 . REPORT_EXECUTIONS_BASE_URL;
        $body = json_encode($req);
        $data = $this->prepAndSend($url, array(200), 'POST', $body, true, 'application/json', 'application/json');
        $result = Execution::createFromJSON($data);
        return $result;
    }

    public function getReportExecutionMetadata($execution_id) {
        $url = $this->restUrl2 . REPORT_EXECUTIONS_BASE_URL . '/' . $execution_id;
        $data = $this->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        $result = Execution::createFromJSON($data);
        return $result;
    }
*/


} // End Client


class RESTRequestException extends \Exception {

	public function __construct($message) {
		$this->message = $message;
	}

}

?>
