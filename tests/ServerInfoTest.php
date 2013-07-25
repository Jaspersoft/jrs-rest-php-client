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

class ServerInfoTest extends BaseTest {

    protected $jc;
    protected $newUser;

    public function setUp() {
		parent::setUp();
		
    }

    public function tearDown() {
		parent::tearDown();
		
    }

    public function testServerInfo() {
        $info = $this->jc->serverInfo();
        $this->assertTrue(isset($info['version']));
    }
}
