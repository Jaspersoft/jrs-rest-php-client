<?php
namespace jaspersoft\service;

use jaspersoft\dto\resource\ResourceLookup;
use jaspersoft\criteria\RepositorySearchCriteria;
use jaspersoft\tools\RESTRequest;

class Repository {

    private $service;
    private $base_url;

    public function __construct(RESTRequest $rest_service, $url) {
        $this->service = $rest_service;
        $this->base_url = $url;
    }

    private function make_url(RepositorySearchCriteria $criteria = null) {
        $result = $this->base_url . '/resources';
        if (!empty($criteria))
            $result .= '?' . $criteria->toQueryParams();
        return $result;
    }

    public function resourceSearch(RepositorySearchCriteria $criteria = null) {
        $result = array();
        $data = $this->service->prepAndSend(self::make_url($criteria), array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        $data = json_decode($data);
        foreach ($data->resourceLookup as $rl)
            $result[] = ResourceLookup::createFromJSON($rl);
        return $result;
    }



}