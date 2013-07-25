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
require_once "BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\Role\Role;


class RoleServiceTest extends BaseTest {


	protected $jc;
	protected $rs;
	protected $newUser;
	protected $newRole;

	/** Coverage: createRole **/
	public function setUp() {
		parent::setUp();
		
	//	$this->newUser = u::createUser();
		$this->rs = $this->jc->roleService();
		$this->newRole = new Role(
				'NOT_A_REAL_ROLE', 'organization_1');
		$this->rs->createRole($this->newRole);	
	}
	
	/** Coverage: deleteRole **/
	public function tearDown() {
		parent::tearDown();
		$this->rs->deleteRole($this->newRole);	
	}

	/* Tests below */

	/** Coverage: updateRole, getRole **/
	public function testUpdateRole()
	{
		$oldName = $this->newRole->roleName;
		$this->newRole->roleName = "ROLE_QA";
		$this->rs->updateRole($this->newRole, $oldName);
		$actual = $this->rs->getRole($this->newRole->roleName, $this->newRole->tenantId);
		$this->assertEquals($this->newRole->roleName, $actual->roleName);
		$this->newRole->roleName = $oldName;
		$this->rs->updateRole($this->newRole, "ROLE_QA");
	}
	
	/** Coverage: getManyRoles **/
	public function testGetManyRoles()
	{
		$roleCount = sizeof($this->rs->getManyRoles());
		$this->assertTrue($roleCount > 2);
	}
	
}

?>