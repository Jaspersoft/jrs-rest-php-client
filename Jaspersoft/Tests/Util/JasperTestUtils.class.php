<?php
namespace Jaspersoft\Tests\Util;

use Jaspersoft\Dto\Role\Role;
use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Resource\Folder;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Dto\Job\Job;

class JasperTestUtils {

	/*
	 * These utilities are used to create objects to work with so that they can be used across the test suite.
	 * These are good examples for the minimum required values when creating objects to use with the server.
	 */
	
	public static function makeID()
	{
		return substr(md5(microtime()), 0, 5);
	}
	
	public static function createJob(Folder $f)
	{
		$uuid = self::makeID();
		$job = new Job;
		$job->baseOutputFilename = 'test';
		$job->repositoryDestination['folderURI'] = $f->uri;
		$job->repositoryDestination['overwriteFiles'] = 'false';
		$job->repositoryDestination['sequentialFilenames'] = 'false';
		$job->description = 'test';
		$job->label = 'test';
		$job->outputFormats['outputFormat'][] = 'PDF';
		$job->outputFormats['outputFormat'][] = 'XLS';
		$job->outputFormats['outputFormat'][] = 'RTF';
		$job->source['reportUnitURI'] = '/reports/samples/AllAccounts';
		$job->trigger->simpleTrigger['recurrenceInterval'] = '1';
		$job->trigger->simpleTrigger['recurrenceIntervalUnit'] = 'DAY';
		$job->trigger->simpleTrigger['occurrenceCount'] = '2';
		$job->trigger->simpleTrigger['startDate'] = '2025-01-26T00:00:00-07:00';
		$job->trigger->simpleTrigger['timezone'] = 'America/Los_Angeles';
		return $job;
	}
	
	public static function createFolder()
	{
		$uuid = self::makeID();
		$entity = new Folder;
		$entity->label = "test_" . $uuid;
		$entity->description = "test folder";
		$entity->uri = "/test_" . $uuid;
		return $entity;
	}
	
	public static function createUser()
	{
		$timecode = self::makeID();

		$role = new Role('ROLE_USER', null, 'false');

		$result = new User();
		$result->setUsername('test_' . $timecode);
		$result->setPassword($timecode);
		$result->setEmailAddress('test@'.$timecode.'.com');
		$result->setFullname('User ' . $timecode);
		$result->setTenantId('organization_1');
		$result->setEnabled('true');
		$result->setExternallyDefined('false');
		$result->addRole($role);
		return $result;
	}
	
	public static function createImage(Folder $f)
	{
		$uuid = self::makeID();
		$entity = new File();
		$entity->label = "file_" . $uuid;
		$entity->description = "test file";
		$entity->uri = $f->uri . "/" . "file_".$uuid;
		return $entity;
	}
	
/* DEPRECATED 
	// Creates an image ResourceDescriptor object to work with
	// You must supply a parent folder ResourceDescriptor object that the image will belong to
	public static function createImage(ResourceDescriptor $folder) {
		//TODO: Make more dynamic. Allow a $folder argument and put it in a test folder to make cleanup simple and isolated
		$timecode = md5(microtime()); // unique id based on time in microseconds (used to create temporary resources)
		// ResourceDescriptor object for an image resource
		$result = new ResourceDescriptor('pitbull' . $timecode, 'img', $folder->getUriString() . '/pitbull' . $timecode, 'true');
		$result->setLabel('pitbull');
		$result->setDescription('photo of a pitbull');
		$result->addProperty(new ResourceProperty('PROP_PARENT_FOLDER', $folder->getUriString()));
		$result->addProperty(new ResourceProperty('PROP_HAS_DATA', 'true'));
		return $result;
	}

	// Creates a folder ResourceDescriptor object to work with
	public static function createFolder() {
		$timecode = md5(microtime()); // unique id based on time in microseconds (used to create temporary resources)
		// ResourceDescriptor Object for a folder resource
		$result = new ResourceDescriptor('test_' . $timecode, 'folder', '/test_' . $timecode, 'false');
		$result->setLabel('test_' . $timecode);
		$result->setDescription('REST Test Folder');
		$result->addProperty(new ResourceProperty('PROP_PARENT_FOLDER', '/'));
		return $result;
	}

	// Create a datasource resourceDescriptor object
	// You must include a ResourceDescriptor object for a folder to put the datasource in as the first argument
	public static function createDataSource(ResourceDescriptor $folder) {
		$timecode = md5(microtime()); // unique id based on time in microseconds (used to create temporary resources)
		// ResourceDescriptor Object for a Data Source
		$result = new ResourceDescriptor($timecode, 'jdbc', $folder->getUriString().'/'.$timecode, 'false');
		$result->setLabel('Test Data Source');
		$result->setDescription('test data source');
		$result->addProperty(new ResourceProperty('PROP_PARENT_FOLDER', $folder->getUriString()));
		$result->addProperty(new ResourceProperty('PROP_DATASOURCE_DRIVER_CLASS', 'org.postgresql.Driver'));
		$result->addProperty(new ResourceProperty('PROP_DATASOURCE_CONNECTION_URL', 'jdbc:postgresql://localhost:5432/sugarcrm'));
		$result->addProperty(new ResourceProperty('PROP_DATASOURCE_USERNAME', 'root'));
		$result->addProperty(new ResourceProperty('PROP_DATASOURCE_PASSWORD', 'password'));
		return $result;
	}

	// Create a Job object with a unique name
	public static function createJob(ResourceDescriptor $folder) {
		$timecode = md5(microtime());

		$result = new Job();
		$result->baseOutputFilename = 'test' . $timecode;
		$result->repositoryDestination['folderURI'] = $folder->getUriString();
		$result->repositoryDestination['overwriteFiles'] = 'false';
		$result->repositoryDestination['sequentialFilenames'] = 'false';
		$result->description = 'test' . $timecode;
		$result->label = 'test' . $timecode;
		$result->outputFormats['outputFormat'][] = 'PDF';
		$result->outputFormats['outputFormat'][] = 'XLS';
		$result->outputFormats['outputFormat'][] = 'RTF';
		$result->source['reportUnitURI'] = '/reports/samples/AllAccounts';
		$result->simpleTrigger['recurrenceInterval'] = '1';
		$result->simpleTrigger['recurrenceIntervalUnit'] = 'DAY';
		$result->simpleTrigger['occurrenceCount'] = '2';
		$result->simpleTrigger['startDate'] = '2025-01-26T00:00:00-07:00';
		$result->simpleTrigger['timezone'] = 'America/Los_Angeles';

		return $result;
	}
*/




}

?>