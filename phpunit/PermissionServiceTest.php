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
use Jasper\JasperTestUtils;
use Jasper\JasperClient;
use Jasper\ResourceDescriptor;
use Jasper\ResourceProperty;
use Jasper\Permission;

require_once(dirname(__FILE__) . '/../client/JasperClient.php');
require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');


class JasperPermissionServiceTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
	protected $jc;

    /** @var JasperClient */
	protected $jcSuper;
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

		// The following is a client authorized as 'superuser' needed for some tests
		$this->jcSuper = new JasperClient(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['super_username'],
				$bootstrap['super_password'],
				$bootstrap['base_url']
				);

		$timecode = md5(microtime());
		$this->test_folder = new ResourceDescriptor($timecode, 'folder', '/' . $timecode, 'false');
		$this->test_folder->setLabel('TestFolder_'.$timecode);
		$this->test_folder->setDescription('REST Test Folder');
		$this->test_folder->addProperty(new ResourceProperty('PROP_PARENT_FOLDER', '/'));
	}

	public function tearDown() {

	}


	/* Tests below */

    /**
     * Checks getPermissions() functionality by verifying whether specific sample resources have
     * specific number of non-inherited Permissions.
     *
     * @todo come up with saner way to check it without relying on pre-set conditions. Not that crucial though.
     */
    public function testGetPermissions_properSizeReturned() {
		$permissionsRoot = $this->jcSuper->getPermissions('/');
		$this->assertEquals(count($permissionsRoot), 3);

		$permissionsReports = $this->jc->getPermissions('/reports');
		$this->assertEquals(sizeof($permissionsReports), 1);
		$this->assertEquals($permissionsReports[0]->getPermissionMask(), 30);
		$this->assertEquals($permissionsReports[0]->getPermissionRecipient()->getRoleName(), 'ROLE_USER');
	}

    /**
     * Checks updatePermissions() - verifies that this method actually sets Permissions for a dummy folder.
     */
    public function testPostPermissions_addsPermissionCorrectly() {
		$this->jc->putResource('/', $this->test_folder);
		$joeuser = $this->jc->getUsers('joeuser');
		$perms[] = new Permission('32', $joeuser[0], $this->test_folder->getUriString());
		$this->jc->updatePermissions($this->test_folder->getUriString(), $perms);
		$test_folder_perms = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->jc->deleteResource($this->test_folder->getUriString());
		$this->assertEquals(sizeof($test_folder_perms), 1);
		$this->assertEquals($test_folder_perms[0]->getPermissionMask(), $perms[0]->getPermissionMask());
		$this->assertEquals($test_folder_perms[0]->getPermissionRecipient()->getUsername(), $perms[0]->getPermissionRecipient()->getUsername());
	}

    /**
     * Checks deletePermissions() - verifies that this method actually removes Permissions for a dummy folder.
     */
    public function testDeletePermissions_deletesPermissionCorrectly() {
		$this->jc->putResource('/', $this->test_folder);
		$joeuser = $this->jc->getUsers('joeuser');
		$perms[] = new Permission('32', $joeuser[0], $this->test_folder->getUriString());
		$this->jc->updatePermissions($this->test_folder->getUriString(), $perms);
		$test_folder_perms = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->assertEquals(sizeof($test_folder_perms), 1);
		$this->jc->deletePermission($test_folder_perms[0]);
		$perms_after_delete = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->assertEquals(sizeof($perms_after_delete), 0);
		$this->jc->deleteResource($this->test_folder->getUriString());
	}

    /**
     * Checks updatePermissions() - verifies that this method actually sets Permissions for a resource
     * different than folder (in this case, an image).
     */
    public function testPostPermissionsToResource_addsPermissionCorrectly() {
        $this->jc->putResource('/', $this->test_folder);
        $resource = JasperTestUtils::createImage($this->test_folder);
        $this->jc->putResource('', $resource, dirname(__FILE__).'/resources/pitbull.jpg');
        $resource = $this->jc->getResource($resource->getUriString());
        $joeuser = $this->jc->getUsers('joeuser');
        $perms = $this->jc->getPermissions($resource->getUriString());

        $perm = new Permission('32', $joeuser[0], $resource->getUriString());
        $perms[] = $perm;
        $this->jc->updatePermissions($resource->getUriString(), $perms);

        $updated_perms = $this->jc->getPermissions($resource->getUriString());

        $this->jc->deleteResource($this->test_folder->getUriString());

        $this->assertEquals(sizeof($perms), sizeof($updated_perms));
        $this->assertEquals($perm->getPermissionMask(), $updated_perms[count($updated_perms)-1]->getPermissionMask());
        $this->assertEquals($perm->getPermissionRecipient(), $updated_perms[count($updated_perms)-1]->getPermissionRecipient());
    }



}

?>
