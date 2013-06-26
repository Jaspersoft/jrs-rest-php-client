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
use Jasper\Organization;

require_once(dirname(__FILE__) . '/../client/JasperClient.php');



class JasperOrganizationServiceTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
    protected $jc;
    protected $testOrg;
    protected $subOrg;

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
    }

    public function tearDown() {
        if ($this->testOrg !== null) {
            $this->jc->deleteOrganization($this->testOrg);
        }

        $this->testOrg = null;
        $this->subOrg = null;
        $this->jc = null;
    }

    /* Tests below */

    /**
     * Checks Organization creation routine - puts it and verifies it's there.
     */
    public function testPutGetOrganization_withoutSubOrganizationFlag() {
        $this->jc->putOrganization($this->testOrg);
        $tempOrg = $this->jc->getOrganization($this->testOrg->getId(), false);
        $this->assertEquals($this->testOrg->getId(), $tempOrg->getId());
    }

    /**
     * Checks nested Organizations creation routine - puts two Organizations
     * (a parent and a child) and checks if the latter is actually a child of the former.
     */
    public function testPutGetOrganization_withSubOrganizationFlag() {
        $this->jc->putOrganization($this->testOrg);
        $this->jc->putOrganization($this->subOrg);

        $tempOrg = $this->jc->getOrganization($this->testOrg->getId(), true);
        $this->assertEquals($this->subOrg->getId(), $tempOrg->getId());

    }

    /**
     * Checks if updating Organization with postOrganization() actually updates it on the server.
     */
    public function testPutPostOrganization_successfullyUpdatesOrganization() {
        $this->jc->putOrganization($this->testOrg);
        $this->testOrg->setTenantDesc('TEST_TEST_TEST');
        $this->jc->postOrganization($this->testOrg);

        $tempOrg = $this->jc->getOrganization($this->testOrg->getId());

        $this->assertEquals('TEST_TEST_TEST', $tempOrg->getTenantDesc());
    }

    /**
     * This function tests the searchOrganization function and invokes createOrganization
     */
    public function testSearchOrganization_createOrganization() {
        $this->jc->createOrganization($this->testOrg);
        $search = $this->jc->searchOrganizations($this->testOrg->id);

        $this->assertEquals($search[0]->id, $this->testOrg->id);
    }

    /**
     * Tests the updateOrganization function for coverage
     */
    public function testUpdateOrganization() {
        $this->jc->createOrganization($this->testOrg);
        $this->testOrg->setTenantDesc('TEST_TEST_TEST');
        $this->jc->updateOrganization($this->testOrg);

        $search = $this->jc->searchOrganizations($this->testOrg->id);
        $this->assertEquals('TEST_TEST_TEST', $search[0]->tenantDesc);
    }

}

?>