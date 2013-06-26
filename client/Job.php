<?php
/* ==========================================================================

Copyright (C) 2005 - 2012 Jaspersoft Corporation. All rights reserved.
http://www.jaspersoft.com.

Unless you have purchased a commercial license agreement from Jaspersoft,
the following license terms apply:

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero  General Public License for more details.

You should have received a copy of the GNU Affero General Public  License
along with this program. If not, see <http://www.gnu.org/licenses/>.

=========================================================================== */

namespace Jasper;

class Job {
/*
    public $alert = array();
	public $baseOutputFilename;
	public $repositoryDestination = array();
	public $creationDate = array();
	public $description;
	public $id;
	public $label;
	public $mailNotification = array();
	public $outputFormats = array();
	public $outputLocale;
	public $source = array();
	public $simpleTrigger = array();
	public $username;
	public $version = 0;
*/


	public function __construct($job_data = null) {
        if (empty($job_data))
            return null;
        foreach ($job_data as $k => $v) {
            $this->$k = $v;
        }
	}
/*
    public function jsonSerialize() {
        return array(
            'alert' => $this->alert,
            'baseOutputFilename' => $this->baseOutputFilename,
            'repositoryDestination' => $this->repositoryDestination,
            'creationDate' => $this->creationDate,
            'description' => $this->description,
            'id' => $this->id,
            'label' => $this->label,
            'mailNotification' => $this->mailNotification,
            'outputFormats' => $this->outputFormats,
            'outputLocale' => $this->outputLocale,
            'source' => $this->source,
            'simpleTrigger' => $this->simpleTrigger,
            'username' => $this->username,
            'version' => $this->version
        );
    }*/

	/**
     * Build XML segment by segment and append it to a preexisting SimpleXMLElement
	 * if a value to a key is an associative array, a new node is created and the data is recursed. If
	 * the value of a key is a non-associative array (numerical) then we will set the key of each element as the same thing.
	 *
	 * @param \SimpleXMLElement &$xml
	 * @param array $data - An array of data to be serialized
	 */
	protected function buildXMLSegment(\SimpleXMLElement &$xml, $data) {
		foreach($data as $k => $v) {
			// Ignore null values
			if(!empty($v) || $v == 0) {
				// If we have an associative array, make a node for our key, then recur through the values
				if (is_array($v) && is_string(key($v))) {
					$kNode = $xml->addChild($k);
					$this->buildXMLSegment($kNode, $v);
				// if we have a numerical array (like for toAddress and outputFormats), use the key of the array containing the numerical array
				} elseif (is_array($v) && is_int(key($v))) {
					foreach($v as $key => $val) {
						$xml->addChild($k, $val);
					}
				// if we don't have an array, make a child node
				} else {
					$xml->addChild($k, $v);
				}
			}
		}
	}

	/**
	 * Represent this object as XML.
     *
	 * @param boolean $asObj - If this is true, return a SimpleXMLElement object instead of a string
	 * @return string|SimpleXMLElement
	 */
	public function toXML($asObj = false) {
		$result = new \SimpleXMLElement('<job></job>');
		$this->buildXMLSegment($result, $this);	// We pass the entire data of this object to our XML Segment builder
		if ($asObj) { return $result; }
		$stripped_result = preg_replace('/\<\?xml(.*)\?\>/', '', $result->asXML());	// Remove the XML prolog
		return $stripped_result;
	}

	public static function createFromXML($xml) {
		$unserializer = new \XML_Unserializer();
		$unserializer->unserialize($xml);
		$data = $unserializer->getUnserializedData();
		$result = new self();

		foreach($data as $k => $v) {
			$result->$k = $v;
		}
		return $result;
	}


}

?>