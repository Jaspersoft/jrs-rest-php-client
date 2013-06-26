<?php
namespace Jasper;

class Permission {

	public $permissionMask;
	public $permissionRecipient;
	public $uri;

	public function __construct($mask = null, $recipient = null, $uri = null) {
		$this->permissionMask = $mask;
		$this->setPermissionRecipient($recipient);
		$this->setUri($uri);
	}

	public static function createFromXML($xml) {
		$result = array();

		$sxi = new \SimpleXMLIterator($xml);
		foreach ($sxi->Item as $item) {
			$recipient = $item->permissionRecipient;
			// we pull the first character off the attribute 'type' to determine if a User or a Role is attached to this data
			$recipient_type = substr(strval($recipient->attributes('xsi', true)->type), 0, 1);
			if($recipient_type == "u") {	// Once we determine, create the correct object to attach to the permission object
				$temp = new PermissionUser(
						strval($recipient->username),
						strval($recipient->fullName),
						strval($recipient->externallyDefined),
						strval($recipient->tenantId)
				);
			} elseif ($recipient_type == "r") {
				$temp = new PermissionRole(
						strval($recipient->name),
						strval($recipient->tenantId),
						strval($recipient->externallyDefined)
				);
			} else {
				throw \Exception('Unknown data returned by server.');
			}
			$tempObject = new Permission(strval($item->permissionMask), $temp, strval($item->URI));
			$result[] = $tempObject;
		}
		return $result;
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
		// The "repo:" of this is cut off when this function is used
		// if you need the raw form access the property without
		// this get function
		if (substr($this->uri, 0, 5) == "repo:") {
			return substr($this->uri, 5);
		} else {
			return $this->uri;
		}
	}

	public function setUri($uri) {
		if (substr($uri, 0, 5) == "repo:") {
			$this->uri = $uri;
		} else {
			$this->uri = "repo:" . $uri;
		}
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
		$result = new self($role->name, $role->tenantId, $role->externallyDefined);
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
		$result = new self($user->username, $user->fullName, $user->externallyDefined, $user->tenantId);
		return $result;
	}

	public function __construct($username, $fullName, $externallyDefined, $tenantId = null) {
		$this->username = $username;
		$this->fullName = $fullName;
		$this->externallyDefined = $externallyDefined;
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
