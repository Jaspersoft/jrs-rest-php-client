<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\Util;

/**
 * Class QueryService
 * @package Jaspersoft\Service
 */
class QueryService extends JRSService
{
    /**
     * This function will execute a query on a data source or domain, and return the results of such query
     *
     * @param string $sourceUri
     * @param string $query
     * @return array
     */
    public function executeQuery($sourceUri, $query)
	{
        $url = $this->service_url . '/queryExecutor' . $sourceUri;
        $data = $this->service->prepAndSend($url, array(200), 'POST', $query, true, 'text/plain', 'application/json');
        return json_decode($data, true);
    }

}