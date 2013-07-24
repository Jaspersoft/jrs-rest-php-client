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
namespace Jaspersoft\Dto\Organization;

/* Jasper\Organization class
 * this class represents Organizations from the JasperServer and contains data that is
 * accessible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/07/2012
 */
class Organization implements \JsonSerializable {

	public $alias;
	public $id;
	public $parentId;
	public $tenantName;
	public $theme;
	public $tenantDesc;
	public $tenantFolderUri;
	public $tenantNote;
	public $tenantUri;

	public function __construct(
		$alias = null,
		$id = null,
		$parentId = null,
		$tenantName = null,
		$theme = null,
		$tenantDesc = null,
		$tenantFolderUri = null,
		$tenantNote = null,
		$tenantUri = null)
	{
        $this->alias = $alias;
        $this->id = $id;
        $this->parentId = $parentId;
        $this->tenantName = $tenantName;
        $this->theme = $theme;
        $this->tenantDesc = $tenantDesc;
        $this->tenantFolderUri = $tenantFolderUri;
        $this->tenantNote = $tenantNote;
        $this->tenantUri = $tenantUri;
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
	public function getAlias() { return $this->alias; }
	public function getId() { return $this->id; }
	public function getParentId() { return $this->parentId; }
	public function getTenantName() { return $this->tenantName; }
	public function getTheme() { return $this->theme; }
	public function getTenantDesc() { return $this->tenantDesc; }
	public function getTenantFolderUri() { return $this->tenantFolderUri; }
	public function getTenantNote() { return $this->tenantNote; }
	public function getTenantUri() { return $this->tenantUri; }


	public function setAlias($alias) { $this->alias = $alias; }
	public function setId($id) { $this->id = $id; }
	public function setParentId($parentId) { $this->parentId = $parentId; }
	public function setTenantName($tenantName) { $this->tenantName = $tenantName; }
	public function setTheme($theme) { $this->theme = $theme; }
	public function setTenantDesc($tenantDesc) { $this->tenantDesc = $tenantDesc; }
	public function setTenantFolderUri($tenantFolderUri) { $this->tenantFolderUri = $tenantFolderUri; }
	public function setTenantNote($tenantNote) { $this->tenantNote = $tenantNote; }
	public function setTenantUri($tenantUri) { $this->tenantUri = $tenantUri; }

	/**
	 * This toString method provides the ability to use the object as an argument in some of the
	 * client features. When it is appended to a URL, it will print the ID.
     *
	 * @return string
	 */
	public function __toString() {
		return $this->id;
	}
}
?>