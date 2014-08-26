<?php


namespace Jaspersoft\Service;



use Jaspersoft\Dto\Domain\MetaData;

class DomainService extends JRSService {

    /**
     * Obtain the metadata about a domain at the given URL
     *
     * @param string $domainUri
     * @return MetaData
     */
    public function getMetadata($domainUri)
    {
        $url = $this->service_url . '/domains'. $domainUri . '/metadata';
        $response = $this->service->prepAndSend($url, array(200), 'GET', null, true);

        return MetaData::createFromJSON(json_decode($response));
    }

} 