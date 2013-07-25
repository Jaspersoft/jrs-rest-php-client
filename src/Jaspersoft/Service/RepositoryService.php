<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Resource\ResourceLookup;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Service\Criteria\RepositorySearchCriteria;
use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;

define("RESOURCE_NAMESPACE", "Jaspersoft\\Dto\\Resource");

class RepositoryService
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
		if (empty($data)) return null;
        $data = json_decode($data);
        foreach ($data->resourceLookup as $rl)
            $result[] = ResourceLookup::createFromJSON($rl);
        return $result;
    }

    /** Obtain an object that fully describes the resource by supplying its ResourceLookup object
     *
     * @param ResourceLookup $lookup
     * @return Resource
     */
    public function getResourceByLookup(ResourceLookup $lookup)
    {
        $url = self::make_url(null, $lookup->uri);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/repository.file+json');

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($lookup->resourceType);
        if (class_exists($class) && is_subclass_of($class, RESOURCE_NAMESPACE . '\\Resource')) {
            return $class::createFromJSON(json_decode($data), $class);
        } else {
            return Resource::createFromJSON(json_decode($data));
        }
    }

    /** Obtain the raw binary data of a file resource stored on the server (e.x: image)
     *
     * @param File $file
     * @return bool|null
     */
    public function getBinaryFileData(File $file)
    {
        $url = self::make_url(null, $file->uri);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', 'application/' . $file->type);
        return $data;
    }

    /** Create a resource using a resource descriptor
     *
     * @param \Jaspersoft\Dto\Resource\Resource $resource Descriptive resource object representing object
     * @param $parentFolder string folder in which the resource should be created
     * @param $createFolders string Create folders in the path that may not exist
     * @param $update boolean Set to true if updating an existing resource
     * @throws \Exception
     * @return ResourceLookup object describing new resource
     */
    public function createResource(Resource $resource, $parentFolder, $createFolders = "true", $update = null)
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
        $verb = ($update) ? 'PUT' : 'POST';
        $data = $this->service->prepAndSend($url, array(201, 200), $verb, $body, true, $file_type, 'application/json');
        return ResourceLookup::createFromJSON(json_decode($data));
    }

    /** Update a resource using a resource descriptor
     *
     * @param $resource Resource Object describing new resource
     * @return ResourceLookup object describing new resource
     */
    public function updateResource(Resource $resource)
    {
        $this->createResource($resource, $resource->uri, null, true);
    }

    /** Create a binary file on the server
     *
     * @param File $resource
     * @param $binaryData
     * @param $mimeType
     * @param $parentFolder
     * @param string $createFolders
     * @return ResourceLookup
     */
    public function createFileResource(File $resource, $binaryData, $mimeType, $parentFolder, $createFolders = "true")
    {
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = $binaryData;
        $response = $this->service->sendBinary($url, array(201), $body, $mimeType, 'attachment; filename=' . $resource->label, $resource->description);
        return ResourceLookup::createFromJSON(json_decode($response));
    }

    /** Copy a resource from one location to another
     *
     * @param $oldLocation
     * @param $newLocation
     * @param null $createFolders
     * @param null $overwrite
     * @return ResourceLookup
     */
    public function copyResource($oldLocation, $newLocation, $createFolders = null, $overwrite = null)
    {
        $url = self::make_url(null, $newLocation);
        if (!empty($createFolders) || !empty($overwrite))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders, "overwrite" => $overwrite));
        $data = $this->service->prepAndSend($url, array(200), 'POST', null, true, 'application/json', 'application/json', array("Content-Location: " . $oldLocation));
        return ResourceLookup::createFromJSON(json_decode($data));
    }

    /** Move a resource from one location to another location within the repository
     *
     * @param $oldLocation
     * @param $newLocation
     * @param null $createFolders
     * @param null $overwrite
     * @return ResourceLookup
     */
    public function moveResource($oldLocation, $newLocation, $createFolders = null, $overwrite = null)
    {
        $url = self::make_url(null, $newLocation);
        if (!empty($createFolders) || !empty($overwrite))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders, "overwrite" => $overwrite));
        $data = $this->service->prepAndSend($url, array(200), 'PUT', null, true, 'application/json', 'application/json', array("Content-Location: " . $oldLocation));
        return ResourceLookup::createFromJSON(json_decode($data));
    }

    /** Remove a resource from the repository
     * @param $uri
     */
    public function deleteResource($uri) {
        $url = self::make_url(null, $uri);
        $this->service->prepAndSend($url, array(204, 200), 'DELETE');
    }

    /** Delete many resources from the repository simultaneously
     *
     * @param $uriArray
     */
    public function deleteManyResources($uriArray) {
        $url = self::make_url() . '?' . Util::query_suffix(array("resourceUri" => $uriArray));
        $this->service->prepAndSend($url, array(204, 200), 'DELETE');
    }

}