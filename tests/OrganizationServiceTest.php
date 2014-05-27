<?php
require_once "BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\Organization\Organization;

class JasperOrganizationServiceTest extends BaseTest {

    protected $jc;
	protected $os;
    protected $testOrg;
    protected $subOrg;

    public function setUp() {
		parent::setUp();
		
        $this->testOrg = new Organization(
            'testorg',
            'testorg',
            'organization_1',
            'testorg'
        );

        $this->subOrg = new Organization(
            'suborg',
            'suborg',
            'testorg',
            'suborg'
        );
		
		$this->os = $this->jc->organizationService();
		$this->os->createOrganization($this->testOrg);
		$this->os->createOrganization($this->subOrg);
    }

    public function tearDown() {
		parent::tearDown();
		$this->os->deleteOrganization($this->subOrg);
		$this->os->deleteOrganization($this->testOrg);
    }

    /* Tests below */

    public function testPutGetOrganization_withoutSubOrganizationFlag()
	{
		$result = $this->os->getOrganization($this->testOrg->id);
		$this->assertEquals($result->id, $this->testOrg->id);
    }

	public function testUpdateOrganization_changesOrganizationData()
	{
		$this->testOrg->tenantDesc = "TEST_TEST";
		$this->os->updateOrganization($this->testOrg);
		$actual = $this->os->getOrganization($this->testOrg->id);
		
		$this->assertEquals($actual->tenantDesc, "TEST_TEST");
	}
	
	public function testSearchOrganization()
	{
		$search = $this->os->searchOrganizations($this->testOrg->id);
		$this->assertTrue(sizeof($search) > 0);
		$this->assertEquals($search[0]->id, $this->testOrg->id);
	}	

}

?>