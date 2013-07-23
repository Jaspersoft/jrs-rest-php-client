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
namespace Jaspersoft\Dto\User;

use Jaspersoft\Dto\Role\Role;

/* Jasper\User class
 * this class represents Users from the JasperServer and contains data that is
 * accessible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/06/2012
 */
class User implements \JsonSerializable {

	public $username;
	public $password;
	public $emailAddress;
	public $fullName;
	public $tenantId;
	public $roles = array();
	public $enabled;
	public $externallyDefined;
	public $previousPasswordChangeTime;

	/**
     * Constructor
	 *
	 * This constructor can be used to populate a User object from scratch
	 * any settings not set at construction can be configured using the SET methods below
	 */
	public function __construct(
		$username = null,
		$password = null,
		$emailAddress = null,
		$fullName = null,
		$tenantId = null,
		// $roles = null,
		$enabled = null,
		$externallyDefined = null,
		$previousPasswordChangeTime = null)
	{
        $this->username = $username;
        $this->password = $password;
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
        $this->tenantId = $tenantId;
        $this->enabled = $enabled;
        $this->externallyDefined = $externallyDefined;
        $this->previousPasswordChangeTime = $previousPasswordChangeTime;
        $this->roles = array();
	}

    public function jsonSerialize() {
        $data = array();
        foreach (get_object_vars($this) as $k => $v) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

	/* Get/Set
	 *
	 */
	public function getEnabled() { return $this->enabled; }
	public function getExternallyDefined() { return $this->externallyDefined; }
	public function getFullName() { return $this->fullName; }
	public function getPassword() { return $this->password; }
	public function getPreviousPasswordChangeTime() { return $this->previousPasswordChangeTime; }
	public function getRoles() { return $this->roles; }
	public function getTenantId() { return $this->tenantId; }
	public function getUsername() { return $this->username; }
	public function getEmailAddress() { return $this->emailAddress; }

	public function setEnabled($enabled) { $this->enabled = $enabled; }
	public function setExternallyDefined($externallyDefined) { $this->externallyDefined = $externallyDefined; }
	public function setFullname($fullName) { $this->fullName = $fullName; }

	public function addRole(Role $role) {
		$this->roles[] = $role;
	}

	public function delRole(Role $role) {
		$data_changed = false;
		for($i = 0; ($i < count($this->roles)) || ($i == -1); $i++) {
			if ($this->roles[$i]->getRoleName() == $role->getRoleName()
					&& $this->roles[$i]->getTenantId() == $role->getTenantId()) {
				unset($this->roles[$i]);
				$data_changed = true;
				$i = -1;
			}
		}
		if($data_changed) { return true; }
		return false;
	}

	/* setPassword automatically sets the previousPasswordChangeTime value
	 * when setting a new password
	 */
	public function setPassword($password) {
		$now = new \DateTime();
		$this->password = $password;
		$this->previousPasswordChangeTime = $now->format('Y-m-d\TH:i:sP');
	}

	public function setTenantId($tenantId) { $this->tenantId = $tenantId; }
	public function setUsername($username) { $this->username = $username; }
	public function setEmailAddress($emailAddress) { $this->emailAddress = $emailAddress; }

}
?>
