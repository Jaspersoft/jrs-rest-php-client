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
use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\User\UserLookup;
use Jaspersoft\Dto\Role\Role;

class UserServiceTest extends BaseTest {

	protected $jc;
	protected $newUser;
	protected $us;

	public function setUp()
	{
		parent::setUp();
		$this->newUser = u::createUser();
		$this->us = $this->jc->userService();
		$this->us->addUser($this->newUser);
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->us->deleteUser($this->newUser);
	}
	
	public function testGetUser_GetsCorrectUser()
	{
		$actual = $this->us->getUser($this->newUser->username, $this->newUser->tenantId);
		$this->assertEquals($this->newUser->fullName, $actual->fullName);
	}
	
	public function testUpdate_ChangesUser()
	{
		$this->newUser->setEmailAddress("test@test.test");
		$this->us->addUser($this->newUser);
		
		$actual = $this->us->getUser($this->newUser->username, $this->newUser->tenantId);
		$this->assertEquals("test@test.test", $actual->emailAddress);
	}
	
	public function testSearchUser_ReturnsAUser()
	{
		$result = $this->us->searchUsers($this->newUser->username);
		$this->assertTrue(sizeof($result) > 0);
		$this->assertTrue($result[0] instanceof UserLookup);
	}
	
	public function testGetUserByLookup_ReturnsCorrectUser()
	{
		$result = $this->us->searchUsers($this->newUser->username);
		$user = $this->us->getUserByLookup($result[0]);
		$this->assertEquals($user->username, $this->newUser->username);
	}

}

?>