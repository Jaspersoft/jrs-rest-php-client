<?php
namespace Jaspersoft\Service;

use Jaspersoft\Tool\Util;
use Jaspersoft\Tool\RESTRequest;


class QueryService
{
	protected $service;
	protected $restUrl2;
	
	public function __construct(RESTRequest $service, $baseUrl)
	{
		$this->service = $service;
		$this->restUrl2 = $baseUrl;
	}
	
    /** This function will execute a query on a data source or domain, and return the results of such query
     *
     * @param $sourceUri - The URI for the data source or domain the query is to be executed on
     * @param $query - String query to be executed on data source/domain
     * @return array
     */
    public function executeQuery($sourceUri, $query)
	{
        $url = $this->restUrl2 . '/queryExecutor' . $sourceUri;
        $data = $this->service->prepAndSend($url, array(200), 'POST', $query, true, 'text/plain', 'application/json');
        return json_decode($data, true);
    }

}

?>