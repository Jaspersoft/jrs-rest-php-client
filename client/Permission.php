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

	public static function createXMLFromArray($permissions) {
		$xml_string = '<entityResource>';
		foreach ($permissions as $perm) {
			$xml_string .= '<Item xsi:type="objectPermissionImpl" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
			$xml_string .= '<permissionMask>' . $perm->getPermissionMask() . '</permissionMask>';
			$xml_string .= $perm->permissionRecipient->asXML();
			$xml_string .= '<URI>' . $perm->uri . '</URI>';
			$xml_string .= '</Item>';
		}
		$xml_string .= '</entityResource>';
		return $xml_string;
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

	public function toXML() {
		$xml_string = '<entityResource><Item xsi:type="objectPermissionImpl" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		$xml_string .= '<permissionMask>' . $this->getPermissionMask() . '</permissionMask>';
		$xml_string .= $this->permissionRecipient->asXML();
		$xml_string .= '<URI>' . $this->uri . '</URI>';
		$xml_string .= '</Item></entityResource>';
		return $xml_string;
	}

	public function __toString() {
		return htmlentities($this->toXML());
	}
} // Permission -- END

class PermissionRole extends Role {

	public static function createFromRole(Role $role) {
		$result = new self($role->getRoleName(), $role->getTenantId(), strval($role->getExternallyDefined()));
		return $result;
	}

	public function __construct($name, $tenantId = null, $externallyDefined = 'false') {
		parent::__construct($name, $tenantId, $externallyDefined);
		$this->_attributes = array('xsi:type' => 'roleImpl');
	}

	public function asXML() {
		$seri_opt = array(
			'indent' => '     ',
			'rootName' => 'permissionRecipient',
			'ignoreNull' => true,
			'attributesArray' => '_attributes'	// see Serializer docs
			);
		$seri = new \XML_Serializer($seri_opt);
		$res = $seri->serialize($this);
		if ($res === true) {
			return $seri->getSerializedData();
		} else {
			return false;
		}
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
		$this->_attributes = array('xsi:type' => 'userImpl');
	}

	public function asXML() {
		$seri_opt = array(
				'indent' => '     ',
				'rootName' => 'permissionRecipient',
				'ignoreNull' => true,
				'attributesArray' => '_attributes'	// see Serializer docs
		);
		$seri = new \XML_Serializer($seri_opt);
		$res = $seri->serialize($this);
		if ($res === true) {
			return $seri->getSerializedData();
		} else {
			return false;
		}
	}
} // PermissionUser -- END

?>
