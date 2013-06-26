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

class ResourceProperty {
	public $name;
	public $value;
	public $children = array();

	public function __construct($name, $value = null) {
		$n = (!empty($name)) ? $name : null;
		$v = (isset($value)) ? $value : null;
		$this->name = $n;
		$this->value = $v;
	}

	public static function createFromXML($xml) {
		$sxi = new \SimpleXMLIterator($xml);

		$temp = new self(strval($sxi->attributes()->name), strval($sxi->value));
		foreach($sxi->resourceProperty as $nestedProp) {
			$temp->children[] = ResourceProperty::createFromXML($nestedProp->asXML());
		}
		return $temp;
	}

	public function newXML() {
		$result = new \SimpleXMLElement('<resourceProperty></resourceProperty>');

		if(!empty($this->name)) { $result->addAttribute('name', $this->name); }
		if(isset($this->value) && $this->value !== "") { $result->addChild('value', $this->value); }

		foreach($this->children as $child) {
			ResourceDescriptor::sxml_append($result, $child->newXML());
		}
		return $result;
	}

	public function getName() { return $this->name; }
	public function getValue() { return $this->value; }
	public function getChildren() { return $this->children; }

	public function setName($name) { $this->name = strval($name); }
	public function setValue($value) { $this->value = strval($value); }
	public function addChild($child) { $this->children[] = $child; }
}

class ResourceDescriptor {

	// root node attributes
	private $_rootAttributes = array();	// array to store rootAttributes

	// children nodes
	private $label;
	private $description;
	private $creationDate;
	private $properties = array();	// nested resourceProperties
	private $children = array();	// nested resourceDescriptors

	public function __construct($name = null, $wsType = null, $uriString = null, $isNew = null) {
		$n = (!empty($name)) ? $name : null;
		$t = (!empty($wsType)) ? $wsType : null;
		$u = (!empty($uriString)) ? $uriString : null;
		$i = (!empty($isNew)) ? $isNew : null;
		$this->_rootAttributes = array('name' => $n,
										'wsType' => $t,
										'uriString' => $u,
										'isNew' => $i);
	}

	/**
     * This class method is used to construct a ResourceDescriptor object by providing the XML for that object.
	 *
	 * @param string $xml - XML String defining ResourceDescriptor
	 * @return ResourceDescriptor
	 */
	public static function createFromXML($xml) {
		$sxi = new \SimpleXMLIterator($xml);

		$temp = new self((string) $sxi->attributes()->name,
				(string) $sxi->attributes()->wsType,
				(string) $sxi->attributes()->uriString,
				(string) $sxi->attributes()->isNew);

		$desc = (!empty($sxi->description)) ? (string) $sxi->description : null;
		$label = (!empty($sxi->label)) ? (string) $sxi->label : null;
		$date = (!empty($sxi->creationDate)) ? (string) $sxi->creationDate : null;

		$temp->setLabel($label);
		$temp->setDescription($desc);
		$temp->setCreationDate($date);

		foreach($sxi->resourceDescriptor as $nestRd) {
			$temp->children[] = ResourceDescriptor::createFromXML($nestRd->asXML());
		}

		foreach($sxi->resourceProperty as $prop) {
			$temp->properties[] = ResourceProperty::createFromXML($prop->asXML());
		}
		return $temp;
	}

	/**
	 * This is the publicly accessible function to display this object as XML.
     *
	 * Using the XML object returned by newXML() we can beautify it (add line breaks and remove version node)
	 * and then return it to whomever is requesting it.
     *
	 * Note: __toString() is an alias for this method.
	 */
	public function toXML() {
		$dom = new \DOMDocument();
		$dom->loadXML($this->newXML()->asXML());
		$dom->formatOutput = true;
		$result = $dom->saveXML();
		// remove the 'xml version="1.0"' node that simpleXML automatically prepends, return this as our result.
		return preg_replace('/<\?xml\sversion.*\?>/', '', $result);
	}

	/**
	 * This class function is used to append one SimpleXMLElement object to another using the DOM package.
	 * Unfortunately by nature the SimpleXML package does not do this.
	 */
	public static function sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) {
		$toDom = dom_import_simplexml($to);
		$fromDom = dom_import_simplexml($from);
		$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
	}

	/**
	 * This function creates the XML recursively through this object and all children objects within.
	 */
	private function newXML() {
		$result = new \SimpleXMLElement('<resourceDescriptor></resourceDescriptor>');

		foreach($this->_rootAttributes as $k => $v) {
			if (!empty($v)) {
				$result->addAttribute($k, $v);
			}
		}

		if(!empty($this->label)) { $result->addChild('label', $this->label); }
		if(!empty($this->description)) { $result->addChild('description', $this->description); }
		if(!empty($this->creationDate)) { $result->addChild('creationDate', $this->creationDate); }
		if(count($this->children) > 0) {
			foreach($this->children as $child) {
				ResourceDescriptor::sxml_append($result, $child->newXML());
			}
		}
		if(count($this->properties) > 0) {
			foreach($this->properties as $property) {
				ResourceDescriptor::sxml_append($result, $property->newXML());
			}
		}
		return $result;
	}

	/**
	 * This object is useful when represented as XML in string form.
	 */
	public function __toString() {
		return htmlentities($this->toXML());
	}

	public function addProperty(ResourceProperty $rp) {
		$this->properties[] = $rp;
	}

	public function delProperty(ResourceProperty $rp) {
		$search = array_search($rp, $this->properties);
		if($search !== FALSE) {
			unset($this->properties[$search]);
		}
	}

	public function setName($name) {
		$this->_rootAttributes['name'] = (!empty($name)) ? strval($name) : null;
	}

	public function getName() {
		return $this->_rootAttributes['name'];
	}

	public function setWsType($wsType) {
		$this->_rootAttributes['wsType'] = $wsType;
	}

	public function getWsType() {
		return $this->_rootAttributes['wsType'];
	}

	public function setUriString($uriString) {
		$this->_rootAttributes['uriString'] = $uriString;
	}

	public function getUriString() {
		return $this->_rootAttributes['uriString'];
	}

	public function setIsNew($isNew) {
		$this->_rootAttributes['isNew'] = (string) $isNew;
	}

	public function getIsNew() {
		return $this->_rootAttributes['isNew'];
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getProperties() {
		return $this->properties;
	}
}
?>