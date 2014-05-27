<?php
require_once __DIR__ . "/BaseTest.php";
use Jaspersoft\Tool\TestUtils as u;

class QueryServiceTest extends BaseTest {

    protected $jc;
    protected $newUser;
    protected $query;

    public function setUp() {
		parent::setUp();
		
		$this->qs = $this->jc->queryService();
        $this->query = <<<EOF
<query>
    <queryFields>
        <queryField id="public_opportunities.amount"/>
        <queryField id="public_opportunities.name"/>
    </queryFields>
</query>
EOF;

    }

    public function tearDown() {
		parent::tearDown();
    }

    public function testQueryExecution() {
        $run = $this->qs->executeQuery('/Domains/Simple_Domain', $this->query);
        // If data is set, then data was collected and the requst was successful
        $this->assertTrue(isset($run['values'][0]['value'][0]));
    }
}
