<?php
namespace Jasper;

/**
 * This class has been deprecated in favor of RepositoryPermission.
 * The class here is intended to be used with legacy functionality of old permisison classes.
 *
 * @deprecated
 */
class Permission {

	public $permissionMask;
	public $permissionRecipient;
	public $uri;
	public $recipientUri;

	public function __construct($mask = null, $recipient = null, $uri = null) {
		if (!empty($mask)) { $this->permissionMask = $mask; }
		if (!empty($recipient)) { $this->setPermissionRecipient($recipient); }
		if (!empty($uri)) { $this->setUri($uri); }
	}

	public function setRecipientUri($uri) {
		$this->recipientUri = $uri;
	}
	
	public function getRecipientUri() {
		return $this->recipientUri;
	}
	
	public function getPermissionMask() {
		return $this->permissionMask;
	}

	public function setPermissionMask($mask) {
		$this->permissionMask = $mask;
	}

	public function getPermissionRecipient() {
		return $this->permissionRecipient;
	}

	public function setPermissionRecipient($recipient) {
		if ($recipient instanceof User) {
			$this->permissionRecipient = ($recipient instanceof PermissionUser) ? $recipient : PermissionUser::createFromUser($recipient);
		} elseif ($recipient instanceof Role) {
			$this->permissionRecipient = ($recipient instanceof PermissionRole) ? $recipient : PermissionRole::createFromRole($recipient);
		} else {
			throw new \Exception('Must provide User or Role object to setPermissionRecipient');
		}
	}

	public function getUri() {
		return $this->uri;
	}

	public function setUri($uri) {
		$this->uri = $uri;
	}

} // Permission -- END

class PermissionRole extends Role {

	public static function createFromRole(Role $role) {
		$result = new self($role->getRoleName(), $role->getTenantId(), strval($role->getExternallyDefined()));
		return $result;
	}

	public function __construct($name, $tenantId = null, $externallyDefined = 'false') {
		parent::__construct($name, $tenantId, $externallyDefined);
	}

} // PermissionRole -- END

class PermissionUser extends User {

	public static function createFromUser(User $user) {
		$result = new self($user->getUsername(), $user->getFullName(), strval($user->externallyDefined), $user->getTenantId());
		return $result;
	}

	public function __construct($username, $fullName, $externallyDefined, $tenantId = null) {
		$this->username = $username;
		$this->fullName = $fullName;
		$this->externallyDefined = strval($externallyDefined);
		$this->tenantId = (!empty($tenantId)) ? strval($tenantId) : null;
	}

} // PermissionUser -- END

?>
