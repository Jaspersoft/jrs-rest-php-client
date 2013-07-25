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

class QueryServiceTest extends BaseTest {

    protected $jc;
    protected $newUser;
    protected $query;

    public function setUp() {
		parent::setUp();
		
		$this->qs = $this->jc->queryService();
        $this->query = <<<EOF
<query>
    <queryFields>
        <queryField id="public_opportunities.amount"/>
        <queryField id="public_opportunities.name"/>
    </queryFields>
</query>
EOF;

    }

    public function tearDown() {
		parent::tearDown();
    }

    public function testQueryExecution() {
        $run = $this->qs->executeQuery('/Domains/Simple_Domain', $this->query);
        // If data is set, then data was collected and the requst was successful
        $this->assertTrue(isset($run['values'][0]['value'][0]));
    }
}
