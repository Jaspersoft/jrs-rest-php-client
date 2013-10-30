<?php
namespace Jaspersoft\Tool;

use Jaspersoft\Dto\Role\Role;
use Jaspersoft\Dto\User\User;
use Jaspersoft\Dto\Resource\Folder;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Dto\Job\Job;

class TestUtils {

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
        $job->label = "Sample Job Name";
        $job->description = "Sample description";
        $job->trigger->simpleTrigger['timezone'] = "America/Los_Angeles";
        $job->trigger->simpleTrigger['startType'] = 2;
        $job->trigger->simpleTrigger['startDate'] = "2025-10-26 10:00";
        $job->trigger->simpleTrigger['occurrenceCount'] = 1;
        $job->source['reportUnitURI'] = "/adhoc/topics/Cascading_multi_select_topic";
        $job->source['parameters']['parameterValues']['Country_multi_select'] = array("Mexico");
        $job->source['parameters']['parameterValues']['Country_name_single_select'] = array("Chin-Lovell Engineering Associates");
        $job->source['parameters']['parameterValues']['Country_state_multi_select'] = array("DF", "Jalisco", "Mexico");
        $job->baseOutputFilename = "Cascading_multi_select_test";
        $job->outputTimeZone = "America/Los_Angeles";
        $job->repositoryDestination['folderURI'] = $f->uri;
        $job->outputFormats['outputFormat'] = array("PDF", "XLS");
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
        $entity->type = "img";
		return $entity;
	}

}

?>
