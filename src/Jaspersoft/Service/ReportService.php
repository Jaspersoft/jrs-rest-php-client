<?php
namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Report\InputControl;

/**
 * Class ReportService
 * @package Jaspersoft\Service
 */
class ReportService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }

    /**
     * This function runs and retrieves the binary data of a report.
     *
     * @param string $uri URI for the report you wish to run
     * @param string $format The format you wish to receive the report in (default: pdf)
     * @param string $pages Request a specific page, or range of pages. Separate multiple pages or ranges by commas.
     *                          (e.g: "1,4-22,42,55-100")
     * @param string $attachmentsPrefix a URI to prefix all image attachment sources with
     *                                  (must include trailing slash if needed)
     * @param array $inputControls associative array of key => value for any input controls
     * @param boolean $interactive Should reports using Highcharts be interactive?
     * @param boolean $onePagePerSheet Produce paginated XLS or XLSX?
     * @param boolean $freshData
     * @param boolean $saveDataSnapshot
     * @param string $transformerKey For use when running a report as a JasperPrint. Specifies print element transformers
     * @return string Binary data of report
     */
	public function runReport($uri, $format = 'pdf', $pages = null, $attachmentsPrefix = null, $inputControls = null,
                                $interactive = true, $onePagePerSheet = false, $freshData = true, $saveDataSnapshot = false, $transformerKey = null)
    {
		$url = $this->restUrl2 . '/reports' . $uri . '.' . $format;
        if (empty($inputControls))
            $url .= '?' . Util::query_suffix(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"));
        else
            $url .= '?' . Util::query_suffix(array_merge(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"), $inputControls));
		$binary = $this->service->prepAndSend($url, array(200), 'GET', null, true);
		return $binary;
	}

	/**
	 * This function will request the possible values and data behind all the input controls of a report.
     *
	 * @param string $uri
	 * @return array
	 */
	public function getReportInputControls($uri)
    {
		$url = $this->restUrl2 . '/reports' . $uri . '/inputControls/values';
		$data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
		return InputControl::createFromJSON($data);
	}

}