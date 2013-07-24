<?php
namespace Jaspersoft\Tests;
use Jaspersoft\Client\Client as c;
use Jaspersoft\Dto\Job\Job;

class BaseTest extends \PHPUnit_Framework_TestCase
{
	public $bootstrap;
	
	public function setUp() {
		$this->bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');
		$this->jc = new c(
				$this->bootstrap['hostname'],
				$this->bootstrap['port'],
				$this->bootstrap['admin_username'],
				$this->bootstrap['admin_password'],
				$this->bootstrap['base_url'],
				$this->bootstrap['admin_org']
		);
	}
	
	public function tearDown() {
		
	}

	
	
}

?>