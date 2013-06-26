<?php
use Jasper\JasperClient;
use Jasper\JasperTestUtils;
use Jasper\ReportOptions;
use Jasper\InputOptions;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperReportOptionsTest extends PHPUnit_Framework_TestCase {

    /** @var JasperClient */
	protected $jc;
	protected $report_uri;
	protected $testSuccess;

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

		$this->report_uri = '/reports/samples/Cascading_multi_select_report';
	}

	public function tearDown() {
		$this->testSuccess = false;
	}

    /**
     * Does a non-strict sanity check on ReportOptions service.
     */
	public function testCreateOptions_CreatesNewOptions() {
		$timecode = substr(md5(microtime()), 0, 5);
		$label = 'test' . $timecode;
		$controlOptions = array('Country_multi_select' => array('USA'));
		$this->jc->updateReportOptions($this->report_uri, $controlOptions, $label, 'true');
		$options = $this->jc->getReportOptions($this->report_uri);
		foreach($options as $o) {
			if ($o->getLabel() == $label) {
				$this->testSuccess = true;
			}
		}
		$this->jc->deleteReportOptions($this->report_uri, $label);
		$this->assertTrue($this->testSuccess);
	}



}
?>