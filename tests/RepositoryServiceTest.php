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
use Jaspersoft\Dto\Resource\JdbcDataSource;
use Jaspersoft\Tool\TestUtils as u;
use Jaspersoft\Dto\Resource\Folder;
use Jaspersoft\Dto\Resource\ResourceLookup;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Service\Criteria\RepositorySearchCriteria;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Exception\ResourceServiceException;
use Jaspersoft\Tool\TestUtils;

class RepositoryServiceTest extends BaseTest {

	protected $jc;
	protected $rs;
	protected $newResource_image;
	protected $newResource_folder;
	public $pwd;

	public function setUp() {
		parent::setUp();

		$this->pwd = dirname(__FILE__);
		$this->image_location = $this->pwd . "/resources/pitbull.jpg";
		$this->rs = $this->jc->repositoryService();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/** Coverage: createFileResource, RepositorySearchCriteria, searchResources
			getResource, getBinaryFileData **/
	public function testCreateImageResource()
	{
		$folder = u::createFolder();
		$img = u::createImage($folder);
		$fileInfo = $this->rs->createFileResource($img, file_get_contents($this->image_location), $folder->uri, true);
		$criteria = new RepositorySearchCriteria();
		$criteria->folderUri = $folder->uri;
		$search = $this->rs->searchResources($criteria);
		$this->assertTrue(sizeof($search->items) > 0);
		
		$file = $this->rs->getResource($search->items[0]->uri);
		$file_data = $this->rs->getBinaryFileData($file);
		$this->assertEquals(file_get_contents($this->image_location), $file_data);
		
		$this->rs->deleteResources($fileInfo->uri);
		$this->rs->deleteResources($folder->uri);
	}
	
	/** Coverage: createResource, searchResources, getResource,
			deleteResources **/
	public function testCreateResource_inFolder()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		
		$criteria = new RepositorySearchCriteria();
		$criteria->q = $folder->label;
		$search = $this->rs->searchResources($criteria);
		$this->assertTrue(sizeof($search->items) > 0);
		$folder_info = $this->rs->getResource($search->items[0]->uri);
		$this->assertEquals($folder_info->label, $folder->label);
		
		$this->rs->deleteResources($folder->uri);
	}

    public function testCreateResource_withArbitraryID()
    {
        $folder = u::createFolder();
        $this->rs->createResource($folder, null, true);

        $actual = $this->rs->getResource($folder->uri);

        $this->assertEquals($folder->uri, $actual->uri);
        $this->assertEquals($folder->label, $actual->label);

        $this->rs->deleteResources($actual->uri);
    }
	
	/** Coverage: updateResource, createResource, searchResources, searchResourcesCriteria, deleteResources **/
	public function testUpdateResource()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		
		$criteria = new RepositorySearchCriteria();
		$criteria->q = $folder->label;
		$search = $this->rs->searchResources($criteria);
		$this->assertTrue(sizeof($search->items) > 0);
		
		$obj = $this->rs->getResource($search->items[0]->uri);
		$obj->label = "test_label";
		$this->rs->updateResource($obj);
		
		$criteria->q = $obj->label;
		$search = $this->rs->searchResources($criteria);
		$this->assertTrue(sizeof($search->items) > 0);
		$this->assertEquals($search->items[0]->label, $obj->label);
		$this->rs->deleteResources($obj->uri);
	}
	
    /** Coverage: createResource, moveResource, searchResources, getResource
			deleteResources **/
    public function testMoveResource()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		$this->rs->moveResource($folder->uri, $folder->uri . "_new", true);
		
		$search = $this->rs->searchResources(new RepositorySearchCriteria($folder->label));
		$obj = $this->rs->getResource($search->items[0]->uri);
		
		$this->assertEquals($obj->uri, $folder->uri . "_new" . $folder->uri);
		
		$this->rs->deleteResources($obj->uri, $folder->uri."_new");
	}

    /**
     * @expectedException \Jaspersoft\Exception\ResourceServiceException
     * @expectedExceptionMessage CreateResource: You must set either the parentFolder parameter or set a URI for the provided resource.
     */
    public function testExceptionThrown_withoutValidURI_createResource()
    {
        $resource = new JdbcDataSource();
        $resource->driverClass = "org.postgresql.Driver";
        $resource->label = u::makeID();
        $resource->description = "TestDS " . $resource->label;
        $resource->password = "None";
        $resource->username = "None";
        $resource->connectionUrl = "jdbc:postgresql://localhost:5432/foodmart";

        $this->rs->createResource($resource, null);

    }
}

?>
