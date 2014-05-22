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
 */
class User  {

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
            if (!empty($v) || $v === false) {
                $data[$k] = $v;
            }
        }
        return $data;
    }


	public function addRole(Role $role) {
		$this->roles[] = $role;
	}

	public function delRole(Role $role) {
		$data_changed = false;
		for($i = 0; ($i < count($this->roles)) || ($i == -1); $i++) {
			if ($this->roles[$i]->name == $role->name
					&& $this->roles[$i]->tenantId == $role->tenantId) {
				unset($this->roles[$i]);
				$data_changed = true;
				$i = -1;
			}
		}
		if($data_changed) { return true; }
		return false;
	}

}
?>
