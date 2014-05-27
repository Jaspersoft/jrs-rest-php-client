<?php
namespace Jaspersoft\Service;

use Jaspersoft\Client\Client;
use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Options\ReportOptions;

/**
 * Class OptionsService
 * @package Jaspersoft\Service
 */
class OptionsService
{
	protected $service;
	protected $restUrl2;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->restUrl2 = $client->getURL();
    }
	
	/**
	 * Get report options
	 *
	 * @param string $uri
	 * @return array
	 */
	public function getReportOptions($uri)
    {
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
	 * @param array $controlOptions
	 * @param string $label
	 * @param boolean $overwrite
	 * @throws \Jaspersoft\Exception\RESTRequestException
     * @return \Jaspersoft\Dto\Options\ReportOptions
	 */
	public function updateReportOptions($uri, $controlOptions, $label, $overwrite)
    {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
        $url .= '?' . Util::query_suffix(array('label' => utf8_encode($label), 'overwrite' => $overwrite));
		$body = json_encode($controlOptions);
		$data = $this->service->prepAndSend($url, array(200), 'POST', $body, true, 'application/json', 'application/json');
        $data_array = json_decode($data, true);
        return new ReportOptions($data_array['uri'], $data_array['id'], $data_array['label']);
	}

	/**
	 * Remove a pre-existing report options. Provide the URI and Label of the report options you wish to remove.
	 * this function is limited in its ability to accept labels with whitespace. If you must delete a report option with whitespace
  	 * in the label name, use the deleteResources function instead. Using the URL to the report option.
     *
	 * @param string $uri
	 * @param string $optionsLabel
     * @throws \Jaspersoft\Exception\RESTRequestException
	 */
	public function deleteReportOptions($uri, $optionsLabel)
    {
		$url = $this->restUrl2 . '/reports' . $uri . '/options/' . $optionsLabel;
		$this->service->prepAndSend($url, array(200), 'DELETE', null, false);
	}
}