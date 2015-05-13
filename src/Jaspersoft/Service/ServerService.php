<?php


namespace Jaspersoft\Service;
use Jaspersoft\Dto\Attribute\Attribute;
use Jaspersoft\Tool\Util;

/**
 * Class ServerService allows you to obtain information about your JRS instance, as well as
 * create and read server attributes
 *
 * @package Jaspersoft\Service
 */
class ServerService extends JRSService
{

    private function makeAttributeUrl($attributeNames = null, $attrName = null)
    {
        $url = $this->service_url . '/attributes';
        if (isset($attributeNames)) {
            $url .= '?' . Util::query_suffix(array('name' => $attributeNames));
        } else if (isset($attrName)) {
            $url .= '/' . str_replace(' ', '%20', $attrName);
        }
        return $url;
    }

    /**
     * Obtain information about the server
     *
     * - Date/Time Formatting Patterns
     * - Edition
     * - Version
     * - Build
     * - Features
     * - License type and expiration
     *
     * @return array
     */
    public function serverInfo()
    {
        $url = $this->service_url . '/serverInfo';
        $data = $this->service->prepAndSend($url, array(200), 'GET', null, true, 'application/json', 'application/json');
        return json_decode($data, true);
    }

    /**
     * Retrieve server attributes
     *
     * @param array $attributeNames
     * @return array
     * @throws \Exception
     */
    public function getAttributes($attributeNames = null)
    {
        $url = self::makeAttributeUrl($attributeNames);
        $data = $this->service->prepAndSend($url, array(200, 204), 'GET', null, true);
        $jsonObj = json_decode($data);
        if (!empty($jsonObj)) {
            $result = array();
            foreach ($jsonObj->attribute as $attr) {
                $result[] = Attribute::createFromJSON($attr);
            }
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Create a new attribute, or update an existing attribute. If created, "true" is returned. If updated,
     * the server-side representation of the attribute is returned.
     *
     * @param \Jaspersoft\Dto\Attribute\Attribute $attribute
     * @return \Jaspersoft\Dto\Attribute\Attribute|bool
     */
    public function addOrUpdateAttribute(Attribute $attribute)
    {
        $url = self::makeAttributeUrl(null, $attribute->name);
        $body = json_encode($attribute);
        $response = $this->service->prepAndSend($url, array(200, 201), 'PUT', $body, true);

        if (!empty($response)) {
            return Attribute::createFromJSON(json_decode($response));
        } else {
            return true;
        }
    }

    /**
     * Replace all existing attributes with the provided set, the server's representation of the attributes will be
     * returned.
     *
     * @param array $attributes
     * @return array
     */
    public function replaceAttributes(array $attributes)
    {
        $url = self::makeAttributeUrl();
        $data = json_encode(array("attribute" => $attributes));

        $replaced = $this->service->prepAndSend($url, array(200), 'PUT', $data, true);
        $replaced = json_decode($replaced);
        $result = array();
        foreach ($replaced->attribute as $attr) {
            $result[] = Attribute::createFromJSON($attr);
        }
        return $result;
    }

    /**
     * Remove all attributes, or specific attributes from an organization.
     *
     * If no attributes are defined, they will all be deleted. Some server attributes are unable to be deleted
     *
     * @param array $attributes An array of attribute names
     * @return bool
     */
    public function deleteAttributes($attributes = null)
    {
        $url = self::makeAttributeUrl();
        if (!empty($attributes))
        {
            $url .= '?' . Util::query_suffix(array('name' => $attributes));
        }
        return $this->service->prepAndSend($url, array(204), 'DELETE', null, false);
    }

}