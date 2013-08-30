<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Dto\Report\InputControl;


class ReportService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}
	
	/**
	 * This function runs and retrieves the binary data of a report.
     *
	 * @param string $uri - URI for the report you wish to run
	 * @param string $format - The format you wish to receive the report in (default: pdf)
	 * @param string $page - Request a specific page
	 * @param string $attachmentsPrefix - a URI to prefix all image attachment sources with (must include trailing slash if needed)
	 * @param array $inputControls - associative array of key => value for any input controls
	 * @return string - the binary data of the report to be handled by external functions
	 */
	public function runReport($uri, $format = 'pdf', $page = null, $attachmentsPrefix = null, $inputControls = null) {
		$url = $this->restUrl2 . '/reports' . $uri . '.' . $format;
		$url .= '?' . preg_replace('/%5B([0-9]{1,})%5D/', null, http_build_query(array('page' => $page) + array('attachmentsPrefix' => $attachmentsPrefix) + (array) $inputControls));
		$binary = $this->service->prepAndSend($url, array(200), 'GET', null, true);
		return $binary;
	}
		
	/**
	 * This function will request the possible values and data behind all the input controls of a report.
     *
	 * @param string $uri
	 * @return Array<InputOptions>
	 */
	public function getReportInputControls($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/inputControls/values';
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return InputControl::createFromJSON($data);
	}

}

?>
