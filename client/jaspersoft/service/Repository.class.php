<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Resource\ResourceLookup;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Service\Criteria\RepositorySearchCriteria;
use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;

define("RESOURCE_NAMESPACE", "Jaspersoft\\Dto\\Resource");

class Repository
{
    private $service;
    private $base_url;

    public function __construct(RESTRequest $rest_service, $url)
    {
        $this->service = $rest_service;
        $this->base_url = $url;
    }

    private function make_url(RepositorySearchCriteria $criteria = null, $uri = null)
    {
        $result = $this->base_url . '/resources';
        if (!empty($criteria))
            $result .= '?' . $criteria->toQueryParams();
        else
            $result = $this->base_url . '/resources' . $uri;
        return $result;
    }

    /** Search repository for objects based on a defined criteria
     *
     * @param RepositorySearchCriteria $criteria
     * @return array of ResourceLookup objects
     */
    public function resourceSearch(RepositorySearchCriteria $criteria = null)
    {
        $url = self::make_url($criteria);
        $result = array();
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');
        $data = json_decode($data);
        foreach ($data->resourceLookup as $rl)
            $result[] = ResourceLookup::createFromJSON($rl);
        return $result;
    }

    public function getResourceByLookup(ResourceLookup $lookup)
    {
        $url = self::make_url(null, $lookup->uri);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/repository.' . $lookup->resourceType . '+json');

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($lookup->resourceType);
        if (class_exists($class) && is_subclass_of($class, RESOURCE_NAMESPACE . '\\Resource')) {
            return $class::createFromJSON(json_decode($data), $class);
        } else {
            throw new \Exception("Unknown Resource Type: " . $lookup->resourceType);
        }
    }

    public function getBinaryFileData(File $file)
    {
        $url = self::make_url(null, $file->uri);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/' . $file->type);
        return $data;
    }

    /** Create a resource using a resource descriptor
     *
     * @param \Jaspersoft\Dto\Resource\Resource $resource Descriptive resource object representing object
     * @param $parentFolder The folder in which the resource should be created
     * @param string $createFolders Create folders in the path that may not exist
     * @throws \Exception
     * @return ResourceLookup object describing new resource
     */
    public function createResource(Resource $resource, $parentFolder, $createFolders = "true")
    {
        if ($resource instanceof File)
            throw new \Exception("Please use createFileResource for File resources");

        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = json_encode($resource);

        // Isolate the class name, lowercase it, and provide it as a filetype in the headers
        $type = explode('\\', get_class($resource));
        $type = lcfirst(end($type));
        $file_type = 'application/repository.' . $type . '+json';

        $data = $this->service->prepAndSend($url, array(201), 'POST', $body, true, $file_type, 'application/json');
        return ResourceLookup::createFromJSON(json_decode($data));
    }

    public function createFileResource(File $resource, $binaryData, $mimeType, $parentFolder, $createFolders = "true")
    {
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = $binaryData;
        $response = $this->service->sendBinary($url, array(201), $body, $mimeType, 'attachment; filename=' . $resource->label, $resource->description);
        return ResourceLookup::createFromJSON(json_decode($response));
    }
}