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
use Jasper\JasperClient;
use Jasper\Role;
use Jasper\User;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperRoleServiceTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
	protected $jc;
	protected $newUser;
	protected $newRole;

	public function setUp() {
		$bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');

		$this->jc = new JasperClient(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['admin_username'],
				$bootstrap['admin_password'],
				$bootstrap['base_url'],
				$bootstrap['admin_org']
				);

		$this->newUser = JasperTestUtils::createUser();

		$this->newRole = new Role(
				'NOT_A_REAL_ROLE', 'organization_1');
		$this->jc->putUsers($this->newUser);
	}

	public function tearDown() {
		if ($this->newUser !== null) {
			$this->jc->deleteUser($this->newUser);
		}
		if ($this->newRole !== null) {
			$this->jc->deleteRole($this->newRole);
		}
		$this->newUser = null;
		$this->newRole = null;
		$this->jc = null;
	}

	/* Tests below */

    /**
     * Checks whether putRole() actually creates a new Role on the server.
     */
    public function testPutRole_addsRole() {
		$this->jc->putRole($this->newRole);
		$newRoleCount = count($this->jc->getRoles($this->newRole->getRoleName(), $this->newRole->getTenantId()));
		$this->assertEquals($newRoleCount, 1);
	}

	/**
     * Checks whether deleteRole() actually deletes the previously created Role from the server.
	 */
	public function testDeleteRole_removesRole() {
		$this->jc->putRole($this->newRole);
		$roleCount = count($this->jc->getRoles($this->newRole->getRoleName(), $this->newRole->getTenantId()));
		$this->jc->deleteRole($this->newRole);
		$this->assertEquals(0, count($this->jc->getRoles($this->newRole->getRoleName(), $this->newRole->getTenantId())));
		$this->newRole = null; 	// must nullify so tearUp doesn't interfere with results
	}

    /**
     * Checks whether postRole() actually updates the previously created Role on the server.
     */
    public function testPostRole_updatesRole() {
		$this->jc->putRole($this->newRole);
		$old_role_name = $this->newRole->getRoleName();
		$this->newRole->setRoleName('ROLE_TESTER');
		$this->jc->postRole($this->newRole, $old_role_name);
		$tempRole = $this->jc->getRoles($this->newRole->getRoleName(), 'organization_1');
		$this->assertEquals($this->newRole->getRoleName(), $tempRole->getRoleName());
	}

    /**
     * getManyRoles test to retrieve all, and createRole test to add one
     */
    public function testCreateRole_getManyRoles() {
        $roleCount = count($this->jc->getManyRoles());
        $this->jc->createRole($this->newRole);
        $newCount = count($this->jc->getManyRoles());
        $this->assertEquals($roleCount + 1, $newCount);
    }

    /**
     * Test get single role function "getRole", also tests that createRole adds role to organization
     */
    public function testGetRole() {
        $this->jc->createRole($this->newRole);
        $serverRole = $this->jc->getRole($this->newRole->roleName, $this->newRole->tenantId);
        $this->assertEquals($this->newRole->roleName, $serverRole->roleName);
    }

    /**
     * Tests the updateRole function
     */
    public function testUpdateRole() {
        $this->jc->createRole($this->newRole);
        $old_role_name = $this->newRole->roleName;
        $this->newRole->setRoleName('ROLE_TESTER');
        $this->jc->updateRole($this->newRole, $old_role_name);
        $serverRole = $this->jc->getRole($this->newRole->roleName, $this->newRole->tenantId);
        $this->assertEquals($serverRole->roleName, 'ROLE_TESTER');
    }
}

?>