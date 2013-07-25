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
use Jaspersoft\Dto\Attribute\Attribute;
use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Role\Role;

class JasperAttributeServiceTest extends BaseTest {

	protected $jc;
	protected $newUser;
	protected $as;
	protected $us;

	public function setUp() {
		parent::setUp();
		$this->newUser = u::createUser();
		$this->attr = new Attribute('Gender', 'Robot');
		$this->attr2 = new Attribute('Favorite Beer', 'Anchor Steam');
		$this->attrArr = array($this->attr, $this->attr2);

		$this->as = $this->jc->attributeService();
		$this->us = $this->jc->userService();
		$this->us->addUsers($this->newUser);
		
	}

	public function tearDown() {
		parent::tearDown();
		$this->us->deleteUser($this->newUser);
	}
	
	/* Tests below */

    /**
     * Checks if user's attribute is saved correctly when postAttributes() is called with Attribute parameter, that is
     * single Attribute.
     */
    public function testPostAttributes_addsOneAttributeData() {
        $this->as->addAttributes($this->newUser, $this->attr);
		$tempAttr = $this->as->getAttributes($this->newUser);
		$tempValue = $tempAttr[0]->getValue();
		$tempName = $tempAttr[0]->getName();

        $this->assertEquals('Robot', $tempValue);
		$this->assertEquals('Gender', $tempName);
	}

    /**
     * Checks if user's attributes are saved correctly when postAttributes() is called with an array of Attributes, that is
     * multiple Attributes.
     */
    public function testPostAttributes_addsMultipleAttributesCount() {
        $attrCount = count($this->as->getAttributes($this->newUser));
        $this->as->addAttributes($this->newUser, $this->attrArr);
        $attr2Value = $this->as->getAttributes($this->newUser);
        $newCount = count($attr2Value);     

        $this->assertEquals($attrCount+2, $newCount);
        $this->assertEquals('Anchor Steam', $attr2Value[1]->getValue());

    }

    /**
     * Checks that updateAttributes() will replace all attributes with those passed to it.
     */
    public function testUpdateAttributes_replacesExistingAttributes() {   
        $this->as->addAttributes($this->newUser, $this->attrArr);
        $attrCount = count($this->as->getAttributes($this->newUser));
        $this->assertEquals(count($this->attrArr), $attrCount);
        $this->as->updateAttributes($this->newUser, array($this->attr));
        $newCount = count($this->as->getAttributes($this->newUser));

        $this->assertNotEquals($attrCount, $newCount);
        $this->assertEquals(1, $newCount);
    }

    /**
     * Invoking all functions to get full coverage
     */
    public function testAttributeAliases_stillWorkProperly() {       
        $this->as->addAttributes($this->newUser, $this->attrArr);
        $attrCount = count($this->as->getAttributes($this->newUser));

        $this->assertEquals(count($this->attrArr), $attrCount);

    }
	
	/**
	 * Deleting attributes
	 */
	 public function testDeleteAttribute() {
		$this->as->addAttributes($this->newUser, $this->attr);
		$count = count($this->as->getAttributes($this->newUser));
		$this->as->deleteAttributes($this->newUser);
		$newcount = count($this->as->getAttributes($this->newUser));
		$this->assertEquals(1, $count);
		$this->assertEquals($newcount, 0);
	}
}
?>
