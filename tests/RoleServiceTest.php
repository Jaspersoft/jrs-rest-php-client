<?php
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
		$oldName = $this->newRole->name;
		$this->newRole->name = "ROLE_QA";
		$this->rs->updateRole($this->newRole, $oldName);
		$actual = $this->rs->getRole($this->newRole->name, $this->newRole->tenantId);
		$this->assertEquals($this->newRole->name, $actual->name);
		$this->newRole->name = $oldName;
		$this->rs->updateRole($this->newRole, "ROLE_QA");
	}
	
	/** Coverage: searchRoles **/
	public function testsearchRoles()
	{
		$roleCount = sizeof($this->rs->searchRoles());
		$this->assertTrue($roleCount > 2);
	}
	
}

?>
