<?php
namespace Jaspersoft\Tests;

use Jaspersoft\Client\Client as c;

class BaseTest extends \PHPUnit_Framework_TestCase
{

	public function setUp() {
		$bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');
		$this->jc = new c(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['admin_username'],
				$bootstrap['admin_password'],
				$bootstrap['base_url'],
				$bootstrap['admin_org']
		);
	}
	
	public function tearDown() {
		
	}
	
	public function testNull() {
		// Avoid warning that no tests exist
	}
	
}

?>