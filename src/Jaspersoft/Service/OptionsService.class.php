<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Options\ReportOptions;

class OptionsService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}
	
	/**
	 * Using this function you can request the report options for a report.
	 *
	 * @param string $uri
	 * @return Array<\Jasper\ReportOptions>
	 */
	public function getReportOptions($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return ReportOptions::createFromJSON($data);
	}

	/**
	 * Update or Create new Report Options.
     *
     * The argument $controlOptions must be an array in the following form:
     *
	 * array('key' => array('value1', 'value2'), 'key2' => array('value1-2', 'value2-2'))
     *
	 * Note that even when there is only one value, it must be encapsulated within an array.
	 *
	 * @param string $uri
	 * @param array<string> $controlOptions
	 * @param string $label
	 * @param string $overwrite
	 * @return bool
	 */
	public function updateReportOptions($uri, $controlOptions, $label, $overwrite) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$url .= '?' . http_build_query(array('label' => utf8_encode($label), 'overwrite' => $overwrite));
		$body = json_encode($controlOptions);
		$data = $this->service->prepAndSend($url, array(200), 'POST', $body, false, 'application/json', 'application/json');
		return $data;
	}

	/**
	 * Remove a pre-existing report options. Provide the URI and Label of the report options you wish to remove.
	 * this function is limited in its ability to accept labels with whitespace. If you must delete a report option with whitespace
  	 * in the label name, use the deleteResource function instead. Using the URL to the report option.
     	 *
	 * @param string $uri
	 * @param string $optionsLabel
	 * @return bool
	 */
	public function deleteReportOptions($uri, $optionsLabel) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options/' . $optionsLabel;
		$data = $this->service->prepAndSend($url, array(200), 'DELETE', null, false);
		return $data;
	}

}
?>