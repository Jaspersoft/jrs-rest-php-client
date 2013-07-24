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
use Jaspersoft\Tests\BaseTest;	
use Jaspersoft\Tests\Util\JasperTestUtils as u;
use Jaspersoft\Dto\Resource\Folder;
use Jaspersoft\Dto\Resource\ResourceLookup;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Service\Criteria\RepositorySearchCriteria;
use Jaspersoft\Dto\Resource\File;

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

	/** Coverage: createFileResource, RepositorySearchCriteria, resourceSearch
			getResourceByLookup, getBinaryFileData **/
	public function testCreateImageResource()
	{
		$folder = u::createFolder();
		$img = u::createImage($folder);
		$fileInfo = $this->rs->createFileResource($img, file_get_contents($this->image_location), 'image/jpg', $folder->uri, true);
		$criteria = new RepositorySearchCriteria();
		$criteria->folderUri = $folder->uri;
		$search = $this->rs->resourceSearch($criteria);
		$this->assertTrue(sizeof($search) > 0);
		
		$file = $this->rs->getResourceByLookup($search[0]);
		$file_data = $this->rs->getBinaryFileData($file);
		$this->assertEquals(file_get_contents($this->image_location), $file_data);
		
		$this->rs->deleteResource($fileInfo->uri);
		$this->rs->deleteResource($folder->uri);
	}
	
	/** Coverage: createResource, resourceSearch, getResourceByLookup,
			deleteResource **/
	public function testCreateResource()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		
		$criteria = new RepositorySearchCriteria();
		$criteria->q = $folder->label;
		$search = $this->rs->resourceSearch($criteria);
		$this->assertTrue(sizeof($search) > 0);
		$folder_info = $this->rs->getResourceByLookup($search[0]);
		$this->assertEquals($folder_info->label, $folder->label);
		
		$this->rs->deleteResource($folder->uri);
	}
	
	public function testUpdateResource()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		
		$criteria = new RepositorySearchCriteria();
		$criteria->q = $folder->label;
		$search = $this->rs->resourceSearch($criteria);
		$this->assertTrue(sizeof($search) > 0);
		
		$obj = $this->rs->getResourceByLookup($search[0]);
		$obj->label = "test_label";
		$this->rs->updateResource($obj);
		
		$criteria->q = $obj->label;
		$search = $this->rs->resourceSearch($criteria);
		$this->assertTrue(sizeof($search) > 0);
		$this->assertEquals($search[0]->label, $obj->label);
		$this->rs->deleteResource($obj->uri);		
	}
	
    /** Coverage: createResource, moveResource, resourceSearch, getResourceByLookup 
			deleteManyResources **/
    public function testMoveResource()
	{
		$folder = u::createFolder();
		$this->rs->createResource($folder, "/", true);
		$this->rs->moveResource($folder->uri, $folder->uri . "_new", true);
		
		$search = $this->rs->resourceSearch(new RepositorySearchCriteria($folder->label));		
		$obj = $this->rs->getResourceByLookup($search[0]);
		
		$this->assertEquals($obj->uri, $folder->uri . "_new" . $folder->uri);
		
		$this->rs->deleteManyResources(array($obj->uri, $folder->uri."_new"));
	}
	
}

?>