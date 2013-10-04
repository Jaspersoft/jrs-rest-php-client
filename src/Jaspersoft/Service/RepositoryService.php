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

    private function make_url(RepositorySearchCriteria $criteria = null, $uri = null, $expanded = null)
    {
        $result = $this->base_url . '/resources';
        if (!empty($criteria))
            $result .= '?' . $criteria->toQueryParams();
        else
            $result = $this->base_url . '/resources' . $uri;
        if (!empty($expanded))
            $result .= '?expanded=true';
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
        if ($lookup->uri == "/")
            $type = "application/repository.folder+json";
        else
            $type = "application/repository.file+json";
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true, 'application/json', $type);

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($lookup->resourceType);
        if (class_exists($class) && is_subclass_of($class, RESOURCE_NAMESPACE . '\\Resource')) {
            return $class::createFromJSON(json_decode($data), $class);
        } else {
            return Resource::createFromJSON(json_decode($data));
        }
    }

    /** Get resource by URI
     *
     * @param string $uri - The URI of the string
     * @param string $expanded - Returns subresources with resource object
     * @return Resource
     */
    public function getResource($uri, $expanded = false)
    {
        if (!$expanded)
            $url = self::make_url(null, $uri);
        else
            $url = self::make_url(null, $uri, true);

        // If getting the root folder, we must use repository.folder+json
        if ($uri == "/")
            $type = "application/repository.folder+json";
        else
            $type = "application/repository.file+json";

        $response = $this->service->makeRequest($url, array(200, 204), 'GET', null, true, 'application/json', $type);

        $data = $response['body'];
        $headers = $response['headers'];
        $content_type = array_values(preg_grep("#repository\.(.*)\+#", $headers));
        preg_match("#repository\.(.*)\+#", $content_type[0], $resource_type);

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($resource_type[1]);
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
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = json_encode($resource);
        // Isolate the class name, lowercase it, and provide it as a filetype in the headers
        $type = explode('\\', get_class($resource));
        $file_type = 'application/repository.' . lcfirst(end($type)) . '+json';
        $verb = ($update) ? 'PUT' : 'POST';
        $data = $this->service->prepAndSend($url, array(201, 200), $verb, $body, true, $file_type, 'application/json');
        return Resource::createFromJSON(json_decode($data), get_class($resource));
    }

    /** Update a resource using a resource descriptor
     *
     * @param $resource Resource Object describing new resource
     * @return ResourceLookup object describing new resource
     */
    public function updateResource(Resource $resource)
    {
        return $this->createResource($resource, $resource->uri, null, true);
    }

    /** Update a file on the server by supplying binary data
     *
     * @param File $resource A resource descriptor for the File
     * @param string $binaryData The binary data of the file to update
     * @param string $mimeType The MIME type of the file you are updating
     * @param string $parentFolder a URI to the folder the file is located in
     */
    public function updateFileResource(File $resource, $binaryData, $mimeType)
    {
        return $this->createFileResource($resource, $binaryData, $mimeType, $resource->uri, true, true);
    }

    /** Create a file on the server by supplying binary data
     *
     * @param File $resource
     * @param $binaryData
     * @param $mimeType string The MIME type of the file
     * @param $parentFolder string The folder to place the file in
     * @param $createFolders boolean
     * @param $update boolean If updating a file resource, set true
     * @return ResourceLookup
     */
    public function createFileResource(File $resource, $binaryData, $mimeType, $parentFolder, $createFolders = true, $update = false)
    {
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = $binaryData;
        $verb = ($update) ? "PUT" : "POST";
        $response = $this->service->sendBinary($url, array(201, 200), $body, $mimeType, 'attachment; filename=' . $resource->label, $resource->description, $verb);
        return File::createFromJSON(json_decode($response), get_class($resource));
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
        $response = $this->service->makeRequest($url, array(200), 'POST', null, true, 'application/json', 'application/json', array("Content-Location: " . $oldLocation));

        $data = $response['body'];
        $headers = $response['headers'];
        $content_type = array_values(preg_grep("#repository\.(.*)\+#", $headers));
        preg_match("#repository\.(.*)\+#", $content_type[0], $resource_type);

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($resource_type[1]);
        if (class_exists($class) && is_subclass_of($class, RESOURCE_NAMESPACE . '\\Resource')) {
            return $class::createFromJSON(json_decode($data), $class);
        } else {
            return Resource::createFromJSON(json_decode($data));
        }

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
        $response = $this->service->makeRequest($url, array(200), 'PUT', null, true, 'application/json', 'application/json', array("Content-Location: " . $oldLocation));

        $data = $response['body'];
        $headers = $response['headers'];
        $content_type = array_values(preg_grep("#repository\.(.*)\+#", $headers));
        preg_match("#repository\.(.*)\+#", $content_type[0], $resource_type);

        $class = RESOURCE_NAMESPACE . '\\' . ucfirst($resource_type[1]);
        if (class_exists($class) && is_subclass_of($class, RESOURCE_NAMESPACE . '\\Resource')) {
            return $class::createFromJSON(json_decode($data), $class);
        } else {
            return Resource::createFromJSON(json_decode($data));
        }
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
