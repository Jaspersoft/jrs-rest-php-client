<?php
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
		$this->us->addOrUpdateUser($this->newUser);
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
		$this->newUser->emailAddress = "test@test.test";
		$this->us->addOrUpdateUser($this->newUser);
		
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