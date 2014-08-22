<?php


namespace Jaspersoft\Service;


use Jaspersoft\Client\Client;

abstract class JRSService {

    protected $service;
    protected $service_url;

    public function __construct(Client &$client)
    {
        $this->service = $client->getService();
        $this->service_url = $client->getURL();
    }

} 