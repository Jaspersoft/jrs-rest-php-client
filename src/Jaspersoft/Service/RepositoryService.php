<?php
namespace Jaspersoft\Service;

use Jaspersoft\Dto\Resource\ResourceLookup;
use Jaspersoft\Dto\Resource\Resource;
use Jaspersoft\Dto\Resource\File;
use Jaspersoft\Service\Criteria\RepositorySearchCriteria;
use Jaspersoft\Service\Result\SearchResourcesResult;
use Jaspersoft\Tool\RESTRequest;
use Jaspersoft\Tool\Util;
use Jaspersoft\Tool\MimeMapper;

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
     * @return \Jaspersoft\Service\Result\SearchResourcesResult
     */
    public function searchResources(RepositorySearchCriteria $criteria = null)
    {
        $url = self::make_url($criteria);
        $response = $this->service->makeRequest($url, array(200, 204), 'GET', null, true, 'application/json', 'application/json');

        $data = $response['body'];
        if (empty($data)) return null;

        $headers = RESTRequest::splitHeaderArray($response['headers']);

        return new SearchResourcesResult(json_decode($data), (int) $headers['Result-Count'], (int) $headers['Start-Index'], (int) $headers['Total-Count']);
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
            return $class::createFromJSON(json_decode($data, true), $class);
        } else {
            return Resource::createFromJSON(json_decode($data, true));
        }
    }

    /** Get resource by URI
     *
     * @param string $uri - The URI of the string
     * @param bool $expanded - Returns subresources with resource object
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
            return $class::createFromJSON(json_decode($data, true), $class);
        } else {
            return Resource::createFromJSON(json_decode($data, true));
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
     * @param string $parentFolder folder in which the resource should be created
     * @param bool $createFolders Create folders in the path that may not exist
     * @param bool $overwrite if true, then resource with given URI will be overwritten even if it is of a different type
     * @throws \Exception
     * @return ResourceLookup object describing new resource
     */
    public function createResource(Resource $resource, $parentFolder, $createFolders = true, $overwrite = false)
    {
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders, "overwrite" => $overwrite));
        $body = $resource->toJSON();
        // Isolate the class name, lowercase it, and provide it as a filetype in the headers
        $type = explode('\\', get_class($resource));
        $file_type = 'application/repository.' . lcfirst(end($type)) . '+json';
        $verb = 'POST';
        $data = $this->service->prepAndSend($url, array(201, 200), $verb, $body, true, $file_type, 'application/json');
        return $resource::createFromJSON(json_decode($data, true), get_class($resource));
    }

    /** Update a resource using a resource descriptor
     *
     * @param \Jaspersoft\Dto\Resource\Resource $resource Object describing new resource
     * @return \Jaspersoft\Dto\Resource\Resource
     */
    public function updateResource(Resource $resource)
    {
        $url = self::make_url(null, $resource->uri);
        $body = $resource->toJSON();
        // Isolate the class name, lowercase it, and provide it as a filetype in the headers
        $type = explode('\\', get_class($resource));
        $file_type = 'application/repository.' . lcfirst(end($type)) . '+json';
        $data = $this->service->prepAndSend($url, array(201, 200), 'PUT', $body, true, $file_type, 'application/json');
        return $resource::createFromJSON(json_decode($data, true), get_class($resource));
    }

    /** Update a file on the server by supplying binary data
     *
     * @param File $resource A resource descriptor for the File
     * @param string $binaryData The binary data of the file to update
     * @return \Jaspersoft\Dto\Resource\Resource
     */
    public function updateFileResource(File $resource, $binaryData)
    {
        $url = self::make_url(null, $resource->uri);

        $body = $binaryData;
        $response = $this->service->sendBinary($url, array(201, 200), $body, MimeMapper::mapType($resource->type), 'attachment; filename=' . $resource->label, $resource->description, 'PUT');
        return File::createFromJSON(json_decode($response, true), get_class($resource));

    }

    /** Create a file on the server by supplying binary data
     *
     * If you are using a custom MIME type, you must add the type => mimeType mapping
     * to the \Jaspersoft\Tool\MimeMapper mimeMap.
     *
     * @param File $resource
     * @param string $binaryData
     * @param string $parentFolder string The folder to place the file in
     * @param bool $createFolders
     * @return ResourceLookup
     */
    public function createFileResource(File $resource, $binaryData, $parentFolder, $createFolders = true)
    {
        $url = self::make_url(null, $parentFolder);
        if (!empty($createFolders))
            $url .= '?' . Util::query_suffix(array("createFolders" => $createFolders));
        $body = $binaryData;
        $response = $this->service->sendBinary($url, array(201, 200), $body, MimeMapper::mapType($resource->type), 'attachment; filename=' . $resource->label, $resource->description, 'POST');
        return File::createFromJSON(json_decode($response, true), get_class($resource));
    }

    /** Copy a resource from one location to another
     *
     * @param string $oldLocation
     * @param string $newLocation
     * @param bool $createFolders
     * @param bool $overwrite
     * @return ResourceLookup
     */
    public function copyResource($oldLocation, $newLocation, $createFolders = true, $overwrite = false)
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
            return $class::createFromJSON(json_decode($data, true), $class);
        } else {
            return Resource::createFromJSON(json_decode($data, true));
        }

    }

    /** Move a resource from one location to another location within the repository
     *
     * @param string $oldLocation
     * @param string $newLocation
     * @param bool $createFolders
     * @param bool $overwrite
     * @return ResourceLookup
     */
    public function moveResource($oldLocation, $newLocation, $createFolders = true, $overwrite = false)
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
            return $class::createFromJSON(json_decode($data, true), $class);
        } else {
            return Resource::createFromJSON(json_decode($data, true));
        }
    }

    /** Remove a resource from the repository
     * @param string $uri
     */
    public function deleteResource($uri) {
        $url = self::make_url(null, $uri);
        $this->service->prepAndSend($url, array(204, 200), 'DELETE');
    }

    /** Delete many resources from the repository simultaneously
     *
     * @param string $uriArray
     */
    public function deleteManyResources($uriArray) {
        $url = self::make_url() . '?' . Util::query_suffix(array("resourceUri" => $uriArray));
        $this->service->prepAndSend($url, array(204, 200), 'DELETE');
    }

}
