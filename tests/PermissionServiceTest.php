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
require_once __DIR__ . "/BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Client\Client;
use Jaspersoft\Dto\Permission\RepositoryPermission;

class PermissionServiceTest extends BaseTest {

	protected $jc;
	protected $jcSuper;
	protected $newUser;

	public function setUp()
	{
		parent::setUp();
				
		$this->jcSuper = new Client(
				$this->bootstrap['hostname'],
				$this->bootstrap['super_username'],
				$this->bootstrap['super_password']
				);
				
		$this->testFolder = u::createFolder();
		$this->testUser = u::createUser();
		$this->jc->repositoryService()->createResource($this->testFolder, "/", true);
		$this->jc->userService()->addUser($this->testUser);
		
		$this->ps = $this->jc->permissionService();
		$this->us = $this->jc->userService();
		$this->super_ps = $this->jcSuper->permissionService();
		
		$this->testPermission = new RepositoryPermission($this->testFolder->uri, "user:/".$this->testUser->tenantId."/".$this->testUser->username, '32');		
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->jc->repositoryService()->deleteResource($this->testFolder->uri);
		$this->jc->userService()->deleteUser($this->testUser);
	}
	
	/** Coverage: createRepositoryPermissions, searchRepositoryPermissions **/
	public function testCreateAndGetPermissions()
	{
		$this->ps->createRepositoryPermissions(array($this->testPermission));
		$search = $this->ps->searchRepositoryPermissions($this->testFolder->uri);
		$this->assertEquals(sizeof($search), 1);
		$this->assertEquals($search[0]->mask, 32);
	}
	
	/** Coverage: create, search, updateRepositoryPermissions **/
	public function testUpdatePermission()
	{
		self::testCreateAndGetPermissions();
		$this->testPermission->mask = 1;
		$this->ps->updateRepositoryPermissions($this->testFolder->uri, array($this->testPermission));
		$search = $this->ps->searchRepositoryPermissions($this->testFolder->uri);
		$this->assertEquals(sizeof($search), 1);
		$this->assertEquals($search[0]->mask, 1);
	}
	/** Coverage: create, search, deleteRepositoryPermissions **/
	public function testDeletePermission()
	{
		self::testCreateAndGetPermissions();
		$this->ps->deleteRepositoryPermission($this->testPermission);
		$search = $this->ps->searchRepositoryPermissions($this->testFolder->uri);
		$this->assertEquals(sizeof($search), 0);
	}

}

?>
