<?php

namespace Jaspersoft\Service;

use Jaspersoft\Tool\Util;
use Jaspersoft\Dto\Thumbnail\ResourceThumbnail;

/**
 * Class ThumbnailService
 * @package Jaspersoft\Service
 */
class ThumbnailService extends JRSService
{

    private function makeUrl($uri = null, $defaultAllowed = null)
    {
        $result = $this->service_url . '/thumbnails';
        if (isset($uri))
        {
            $result .= $uri;
        }
        if (isset($defaultAllowed)) {
            if (is_bool($defaultAllowed)) {
                $defaultAllowedValue = ($defaultAllowed) ? 'true' : 'false';
                $result .= '?defaultAllowed=' . $defaultAllowedValue;
            } else {
                $result .= '?defaultAllowed=' . $defaultAllowed;
            }
        }
        return $result;
    }

    /**
     * Obtain a String of a thumbnail encoded in Base64
     *
     * @param $uri
     * @param bool $defaultAllowed
     * @return mixed
     */
    public function getThumbnailAsBase64String($uri, $defaultAllowed = false)
    {
        $url = self::makeUrl($uri, $defaultAllowed);
        $response = $this->service->makeRequest($url, array(200, 204), 'GET', null, true, 'application/json', 'text/plain');

        return $response['body'];
    }

    /**
     * Obtain a thumbnail as binary JPEG data
     *
     * @param $uri
     * @param bool $defaultAllowed
     * @return mixed
     */
    public function getThumbnailAsJpeg($uri, $defaultAllowed = false)
    {
        $url = self::makeUrl($uri, $defaultAllowed);
        $response = $this->service->makeRequest($url, array(200, 204), 'GET', null, true, 'application/json', 'image/jpeg');

        return $response['body'];
    }

    /**
     * Obtain a set of ResourceThumbnail objects representing the thumbnails for several reports,
     * these ResourceThumbnail objects will contain the image data as a Base64 String only
     *
     * @param array $uris
     * @param bool $defaultAllowed
     * @return array
     */
    public function getResourceThumbnails(array $uris, $defaultAllowed = false)
    {
        $result = array();
        $url = self::makeUrl();
        $postString = Util::query_suffix(array("uri" => $uris, "defaultAllowed" => $defaultAllowed));

        $response = $this->service->makeRequest($url, array(200), 'POST', $postString, true, 'application/x-www-form-urlencoded');
        $data = json_decode($response['body']);
        if (isset($data)) {
            foreach ($data as $thumbnail) {
                $result[] = ResourceThumbnail::createFromJSON($thumbnail);
            }
        }
        return $result;
    }


}