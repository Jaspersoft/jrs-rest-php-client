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
use Jaspersoft\Client\Client as JasperClient;
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