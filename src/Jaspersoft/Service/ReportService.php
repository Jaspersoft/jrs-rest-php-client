<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Report\InputControls\InputControl;
use Jaspersoft\Dto\Report\InputControls\InputControlState;
use Jaspersoft\Tool\Util;

/**
 * Class ReportService
 * @package Jaspersoft\Service
 */
class ReportService extends JRSService
{

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
		$url = $this->service_url . '/reports' . $uri . '.' . $format;
        if (empty($inputControls))
            $url .= '?' . Util::query_suffix(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"));
        else
            $url .= '?' . Util::query_suffix(array_merge(compact("pages", "attachmentsPrefix", "interactive", "onePagePerSheet", "freshData", "saveDataSnapshot", "transformerKey"), $inputControls));
		$binary = $this->service->prepAndSend($url, array(200), 'GET', null, true);
		return $binary;
	}

	/**
	 * Returns a set of InputControl items defining the values of InputControls
     *
     * @deprecated Use getInputControlValues instead
	 * @param string $uri
	 * @return array
	 */
	public function getReportInputControls($uri)
    {
        $url = $this->service_url . '/reports' . $uri . '/inputControls/values';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        $json_obj = json_decode($data);
        $result = array();

        foreach ($json_obj->inputControlState as $state) {
            $result[] = \Jaspersoft\Dto\Report\InputControl::createFromJSON($state);
        }
        return $result;
	}

    /**
     * Returns a set of InputControl items defining the values of InputControls
     *
     * @param string $uri
     * @return array
     */
    public function getInputControlValues($uri)
    {
        $url = $this->service_url . '/reports' . $uri . '/inputControls/values';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        $json_obj = json_decode($data);
        $result = array();

        foreach ($json_obj->inputControlState as $state) {
            $result[] = InputControlState::createFromJSON($state);
        }
        return $result;
    }


    /**
     * Returns a set of InputControl objects defining input controls for a report
     *
     * @param string $uri Report to obtain structure from
     * @return array
     */
    public function getInputControlStructure($uri) {
        $url = $this->service_url . '/reports' . $uri . '/inputControls';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true);
        $json_obj = json_decode($data);

        $result = array();
        foreach ($json_obj->inputControl as $control) {
            $result[] = InputControl::createFromJSON($control);
        }
        return $result;
    }

    /**
     * Update the order of a report's input control structure.
     *
     * The second argument must only contain elements that have not been changed except for their order since being
     * retrieved from getReportInputControlStructure.
     *
     * @param string $uri
     * @param array $controls A set of InputControl
     * @return array The new structure
     */
    public function updateInputControlOrder($uri, array $controls)
    {
        $url = $this->service_url . '/reports' . $uri . '/inputControls';
        $body = array("inputControl" => array());
        foreach ($controls as $control) {
            if ($control instanceof InputControl) {
                $body['inputControl'][] = $control->jsonSerialize();
            }
        }
        $response = $this->service->prepAndSend($url, array(200), 'PUT', json_encode($body), true);

        $json_obj = json_decode($response);
        $result = array();
        foreach ($json_obj->inputControl as $control) {
            $result[] = InputControl::createFromJSON($control);
        }
        return $result;
    }


    /**
     * Update the values of a report's input controls, and obtain the updated input control states as a result
     *
     * @param string $uri
     * @param array $parameters Set of parameters in format: array("id" => array("value"), "id2" => array("value2"));
     * @return array
     */
    public function updateInputControlValues($uri, array $parameters)
    {
        $url = $this->service_url . '/reports' . $uri . '/inputControls/' . join(';', array_keys($parameters)) . '/values';
        $response = $this->service->prepAndSend($url, array(200), 'POST', json_encode($parameters), true);

        $json_obj = json_decode($response);
        $result = array();
        foreach ($json_obj->inputControlState as $state) {
            $result[] = InputControlState::createFromJSON($state);
        }
        return $result;
    }

    /**
     * Update the values of a report's input controls, and obtain the new structure as a result
     *
     * @param string $uri
     * @param array $parameters Set of parameters in format: array("id" => array("value"), "id2" => array("value2"));
     * @return array Set of InputControl objects
     */
    public function updateInputControls($uri, array $parameters)
    {
        $url = $this->service_url . '/reports' . $uri . '/inputControls';
        $response = $this->service->prepAndSend($url, array(200), 'POST', json_encode($parameters), true);

        $json_obj = json_decode($response);
        $result = array();
        foreach ($json_obj->inputControl as $control) {
            $result[] = InputControl::createFromJSON($control);
        }
        return $result;
    }

}