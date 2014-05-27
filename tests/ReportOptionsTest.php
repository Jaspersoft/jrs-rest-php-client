<?php
require_once __DIR__ . "/BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;

class ReportOptionsTest extends BaseTest {

	protected $jc;
	protected $report_uri;
	protected $testSuccess;

	public function setUp() {
		parent::setUp();
		$this->os = $this->jc->optionsService();
		$this->report_uri = '/reports/samples/Cascading_multi_select_report';
		$this->testSuccess = false;
	}

	public function tearDown() {
		parent::tearDown();
		$this->testSuccess = false;
	}

    /**
     * Does a non-strict sanity check on ReportOptions service.
     */
	public function testCreateOptions_CreatesNewOptions() {
		$timecode = substr(md5(microtime()), 0, 5);
		$label = 'test' . $timecode;
		$controlOptions = array('Country_multi_select' => array('USA'));
		$this->os->updateReportOptions($this->report_uri, $controlOptions, $label, 'true');
		$options = $this->os->getReportOptions($this->report_uri);
		foreach($options as $o) {
			if ($o->label == $label) {
				$this->testSuccess = true;
			}
		}
		$this->os->deleteReportOptions($this->report_uri, $label);
		$this->assertTrue($this->testSuccess);
	}



}
?>