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
use Jasper\User;
use Jasper\Role;
use Jasper\RESTRequestException;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperUserServiceTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
	protected $jc;
	protected $newUser;

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
	}

	public function tearDown() {

	}

	/* Tests below */

    /**
     * Checks getUser() by getting a previously created User and checking their similarity.
     */
    public function testGetUser_getsCorrectUser() {
		$this->jc->putUsers($this->newUser);
		$tempUser = $this->jc->getUsers($this->newUser->getUsername());
		$tempUser = $tempUser[0];
		$this->jc->deleteUser($tempUser);
		$this->assertEquals($this->newUser->getFullName(), $tempUser->getFullName());
	}

    /**
     * Checks putUsers() by verifying if a number of Users has incremented by 1.
     */
    public function testCreateUser_increasesUserCountByOne() {
		$userCount = count($this->jc->getUsers());
		$this->jc->putUsers($this->newUser);
		$this->assertEquals($userCount+1, (count($this->jc->getUsers())));
        $this->jc->deleteUser($this->newUser);
	}

	/**
     * Checks deleteUser() by verifying whether a number of Users left unchanged after creating and immediately deleting
     * a dummy user. Depends on putUsers() proper functioning.
     *
	 * @depends testCreateUser_increasesUserCountByOne
	 */
	public function testDeleteUser_reducesUserCountByOne() {
		$userCount = count($this->jc->getUsers());
		$this->jc->putUsers($this->newUser);
		$this->jc->deleteUser($this->newUser);
		$this->assertEquals($userCount, count($this->jc->getUsers()));
	}

	/**
     * Checks whether postUser() actually changes User's data.
     *
	 * @depends testCreateUser_increasesUserCountByOne
	 */
	public function testPostUser_changesUserData() {
		$this->jc->putUsers($this->newUser);
		$this->newUser->setEmailAddress('test@dude.com');
		$this->jc->postUser($this->newUser);
		$tempUser = $this->jc->getUsers($this->newUser->getUsername());
		$tempUser = $tempUser[0];
		$this->jc->deleteUser($tempUser);
		$this->assertEquals($tempUser->getEmailAddress(), 'test@dude.com');
	}

	/**
     * This test expects and exception by trying to delete a User that should not exist.
     *
	 * @expectedException Jasper\RESTRequestException
	 */
    public function testDeleteUser_thatDoesNotExist() {
		$this->jc->deleteUser($this->newUser);
	}

    /**
     * Checks addRole() for actually adding a Role to User.
     */
    public function testAddingARole_actuallyAddsARole() {
		$user = JasperTestUtils::createUser();
		$role = new Role('ROLE_DEMO', null, 'false');
		$this->jc->putUsers($user);
		$userServerObj_beforePost = $this->jc->getUsers($user->getUsername());
		$userServerObj_beforePost = $userServerObj_beforePost[0];

		$user->addRole($role);
		$this->jc->postUser($user);
		$userServerObj_afterPost = $this->jc->getUsers($user->getUsername());
		$userServerObj_afterPost = $userServerObj_afterPost[0];

		$this->jc->deleteUser($user);

		$this->assertEquals((count($userServerObj_beforePost->getRoles()) + 1), count($userServerObj_afterPost->getRoles()));
	}

    /**
     * Checks delRole() by actually deleting a Role from User.
     */
    public function testRevokingARole_actuallyRevokesARole() {
        $user = JasperTestUtils::createUser();
        $role = new Role('ROLE_DEMO', null, 'false');
        $user->addRole($role);
        $this->jc->putUsers($user);

        $createdUser = $this->jc->getUsers($user->getUsername());
        $createdUser = $createdUser[0];
        $this->assertEquals(count($user->getRoles()), count($createdUser->getRoles()));

        $user->delRole($role);
        $this->jc->postUser($user);

        $updatedUser = $this->jc->getUsers($user->getUsername());
        $updatedUser = $updatedUser[0];
        $this->jc->deleteUser($user);

        $this->assertEquals(count($user->getRoles()), count($updatedUser->getRoles()));
        $this->assertEquals(count($createdUser->getRoles()) - 1, count($updatedUser->getRoles()));
    }

    /**
     * Ensure searchUsers returns UserLookup objects, and that getUserByLookup returns correct user
     */
    public function testSearchUsers_returnsUserLookupOfProperUser() {
        $user = JasperTestUtils::createUser();
        $this->jc->addUsers($user);
        $search = $this->jc->searchUsers($user->getUsername());
        $this->assertTrue($search[0] instanceof \Jasper\UserLookup );
        $userByLookup = $this->jc->getUserByLookup($search[0]);
        $this->assertTrue($userByLookup->getUsername() === $user->getUsername());
        $this->jc->deleteUser($user);
    }

    /**
     * Test the getUser function to make sure it pulls the proper User object from the server
     */
    public function testGetUser_getsUser() {
        $user = JasperTestUtils::createUser();
        $this->jc->addUsers($user);
        $get = $this->jc->getUser($user->getUsername(), $user->getTenantId());
        $this->assertTrue($user->getUsername() === $get->getUsername());
        $this->jc->deleteUser($user);
    }
}

?>